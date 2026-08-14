// Package pdfgen génère les PDF récapitulatifs de collectes et tournées.
package pdfgen

import (
	"bytes"
	"fmt"
	"image"
	"image/draw"
	"image/png"
	"os"
	"regexp"
	"time"

	"github.com/boombuler/barcode"
	"github.com/boombuler/barcode/ean"
	"github.com/jung-kurt/gofpdf"
)

var reChiffres = regexp.MustCompile(`^\d+$`)

// genererImageCodeBarre construit un PNG EAN-13 pour le code donné. Certains codes
// de démonstration présents en base n'ont pas une clé de contrôle EAN-13 valide ; on
// se base donc uniquement sur les 12 premiers chiffres et on laisse la bibliothèque
// recalculer la clé, ce qui garantit un code-barres toujours valide et scannable.
func genererImageCodeBarre(code string) ([]byte, error) {
	if len(code) < 12 || !reChiffres.MatchString(code) {
		return nil, fmt.Errorf("code-barres non numérique ou trop court")
	}
	bc, err := ean.Encode(code[:12])
	if err != nil {
		return nil, err
	}
	img, err := barcode.Scale(bc, 300, 100)
	if err != nil {
		return nil, err
	}
	// Reconvertit en RGBA 8 bits/canal : gofpdf ne sait pas lire les PNG 16 bits que
	// l'encodeur standard peut produire à partir du modèle de couleur du code-barres.
	rgba := image.NewRGBA(img.Bounds())
	draw.Draw(rgba, img.Bounds(), img, image.Point{}, draw.Src)

	var buf bytes.Buffer
	if err := png.Encode(&buf, rgba); err != nil {
		return nil, err
	}
	return buf.Bytes(), nil
}

// Couleurs de la charte (vert NO MORE WASTE)
var (
	couleurPrimaire   = [3]int{25, 135, 84} // vert (bootstrap success)
	couleurTexteFonce = [3]int{33, 37, 41}
	couleurTexteGris  = [3]int{108, 117, 125}
	couleurBordure    = [3]int{222, 226, 230}
	couleurLigneAlt   = [3]int{245, 247, 246}
)

type LigneInfo struct {
	Label  string
	Valeur string
}

type LigneBenevole struct {
	ID     int
	Nom    string
	Prenom string
	Role   string // ex: "Chauffeur" — laisser vide si non applicable
}

type LigneProduit struct {
	ID        int
	CodeBarre string
	Nom       string
	Quantite  int
}

// DocumentRecap regroupe toutes les données nécessaires au récapitulatif,
// qu'il s'agisse d'une collecte ou d'une tournée.
type DocumentRecap struct {
	TypeDocument   string // "Collecte" ou "Tournée"
	NumeroID       int
	Statut         string
	DateDebut      time.Time
	LabelDateDebut string
	DateFin        *time.Time
	Adresses       []LigneInfo
	Complements    []LigneInfo
	Benevoles      []LigneBenevole
	Produits       []LigneProduit
	TitreProduits  string // "Produits collectés" / "Produits livrés"
}

// contexte transporte le PDF et le traducteur UTF-8 -> CP1252 requis par les
// polices standard de gofpdf (sans quoi les caractères accentués sont corrompus).
type contexte struct {
	pdf *gofpdf.Fpdf
	tr  func(string) string
}

func (c contexte) t(s string) string { return c.tr(s) }

// tronquer raccourcit le texte avec une ellipse s'il dépasse la largeur disponible,
// pour ne jamais empiéter sur la cellule suivante (ex : nom de produit trop long).
func tronquer(c contexte, texte string, largeurMax float64) string {
	texte = c.t(texte)
	if c.pdf.GetStringWidth(texte) <= largeurMax {
		return texte
	}
	suffixe := "..."
	for len([]rune(texte)) > 0 && c.pdf.GetStringWidth(texte+suffixe) > largeurMax {
		r := []rune(texte)
		texte = string(r[:len(r)-1])
	}
	return texte + suffixe
}

// Generer construit le PDF et retourne son contenu binaire.
func Generer(doc DocumentRecap, logoPath string) ([]byte, error) {
	pdf := gofpdf.New("P", "mm", "A4", "")
	pdf.SetMargins(15, 15, 15)
	pdf.SetAutoPageBreak(true, 20)
	pdf.AliasNbPages("")
	tr := pdf.UnicodeTranslatorFromDescriptor("")
	c := contexte{pdf: pdf, tr: tr}

	pdf.SetFooterFunc(func() {
		pdf.SetY(-15)
		pdf.SetFont("Helvetica", "I", 8)
		pdf.SetTextColor(couleurTexteGris[0], couleurTexteGris[1], couleurTexteGris[2])
		pdf.CellFormat(0, 10, tr(fmt.Sprintf("Document généré automatiquement par NO MORE WASTE — Page %d/{nb}", pdf.PageNo())), "", 0, "C", false, 0, "")
	})

	pdf.AddPage()

	dessinerEntete(c, doc, logoPath)
	dessinerInfosGenerales(c, doc)
	if len(doc.Complements) > 0 {
		dessinerLigneInfos(c, "Détails", doc.Complements)
	}
	dessinerBenevoles(c, doc.Benevoles)
	dessinerProduits(c, doc)

	var buf bytes.Buffer
	if err := pdf.Output(&buf); err != nil {
		return nil, err
	}
	return buf.Bytes(), nil
}

func dessinerEntete(c contexte, doc DocumentRecap, logoPath string) {
	pdf := c.pdf
	if logoPath != "" {
		if _, err := os.Stat(logoPath); err == nil {
			pdf.ImageOptions(logoPath, 15, 12, 22, 0, false, gofpdf.ImageOptions{ImageType: "", ReadDpi: true}, 0, "")
		}
	}

	pdf.SetXY(42, 14)
	pdf.SetFont("Helvetica", "B", 16)
	pdf.SetTextColor(couleurPrimaire[0], couleurPrimaire[1], couleurPrimaire[2])
	pdf.CellFormat(0, 8, "NO MORE WASTE", "", 2, "L", false, 0, "")

	pdf.SetX(42)
	pdf.SetFont("Helvetica", "", 9)
	pdf.SetTextColor(couleurTexteGris[0], couleurTexteGris[1], couleurTexteGris[2])
	pdf.CellFormat(0, 5, c.t("Ensemble contre le gaspillage alimentaire"), "", 2, "L", false, 0, "")

	pdf.SetY(14)
	pdf.SetFont("Helvetica", "B", 13)
	pdf.SetTextColor(couleurTexteFonce[0], couleurTexteFonce[1], couleurTexteFonce[2])
	pdf.CellFormat(0, 6, c.t(fmt.Sprintf("%s n°%d", doc.TypeDocument, doc.NumeroID)), "", 2, "R", false, 0, "")

	pdf.SetFont("Helvetica", "", 9)
	pdf.SetTextColor(couleurTexteGris[0], couleurTexteGris[1], couleurTexteGris[2])
	pdf.CellFormat(0, 5, c.t("Généré le "+time.Now().Format("02/01/2006 à 15:04")), "", 2, "R", false, 0, "")
	pdf.CellFormat(0, 5, c.t("Statut : "+doc.Statut), "", 2, "R", false, 0, "")

	pdf.SetY(38)
	pdf.SetDrawColor(couleurPrimaire[0], couleurPrimaire[1], couleurPrimaire[2])
	pdf.SetLineWidth(0.6)
	pdf.Line(15, 38, 195, 38)
	pdf.Ln(8)
}

func dessinerTitreSection(c contexte, titre string) {
	pdf := c.pdf
	pdf.Ln(3)
	pdf.SetFillColor(couleurPrimaire[0], couleurPrimaire[1], couleurPrimaire[2])
	pdf.SetTextColor(255, 255, 255)
	pdf.SetFont("Helvetica", "B", 10)
	pdf.CellFormat(0, 7, c.t("  "+titre), "", 1, "L", true, 0, "")
	pdf.SetTextColor(couleurTexteFonce[0], couleurTexteFonce[1], couleurTexteFonce[2])
	pdf.Ln(2)
}

func dessinerInfosGenerales(c contexte, doc DocumentRecap) {
	dessinerTitreSection(c, "Informations générales")

	dateFinStr := "—"
	if doc.DateFin != nil {
		dateFinStr = doc.DateFin.Format("02/01/2006 à 15:04")
	}
	lignes := []LigneInfo{
		{Label: doc.LabelDateDebut, Valeur: doc.DateDebut.Format("02/01/2006 à 15:04")},
		{Label: "Date de fin", Valeur: dateFinStr},
	}
	lignes = append(lignes, doc.Adresses...)
	dessinerLigneInfos(c, "", lignes)
}

func dessinerLigneInfos(c contexte, titre string, lignes []LigneInfo) {
	pdf := c.pdf
	if titre != "" {
		dessinerTitreSection(c, titre)
	}
	for _, l := range lignes {
		pdf.SetFont("Helvetica", "B", 10)
		pdf.CellFormat(50, 7, c.t(l.Label), "", 0, "L", false, 0, "")
		pdf.SetFont("Helvetica", "", 10)
		pdf.MultiCell(0, 7, c.t(l.Valeur), "", "L", false)
	}
	pdf.Ln(2)
}

func dessinerBenevoles(c contexte, benevoles []LigneBenevole) {
	pdf := c.pdf
	dessinerTitreSection(c, fmt.Sprintf("Bénévoles ayant participé (%d)", len(benevoles)))

	if len(benevoles) == 0 {
		pdf.SetFont("Helvetica", "I", 9)
		pdf.SetTextColor(couleurTexteGris[0], couleurTexteGris[1], couleurTexteGris[2])
		pdf.CellFormat(0, 6, c.t("Aucun bénévole enregistré."), "", 1, "L", false, 0, "")
		pdf.SetTextColor(couleurTexteFonce[0], couleurTexteFonce[1], couleurTexteFonce[2])
		pdf.Ln(2)
		return
	}

	largeurs := []float64{20, 60, 60, 40}
	entetes := []string{"ID", "Nom", "Prénom", "Rôle"}

	pdf.SetFont("Helvetica", "B", 9)
	pdf.SetFillColor(couleurBordure[0], couleurBordure[1], couleurBordure[2])
	for i, e := range entetes {
		pdf.CellFormat(largeurs[i], 7, c.t(e), "1", 0, "L", true, 0, "")
	}
	pdf.Ln(-1)

	pdf.SetFont("Helvetica", "", 9)
	for i, b := range benevoles {
		remplir := i%2 == 1
		if remplir {
			pdf.SetFillColor(couleurLigneAlt[0], couleurLigneAlt[1], couleurLigneAlt[2])
		}
		role := b.Role
		if role == "" {
			role = "—"
		}
		pdf.CellFormat(largeurs[0], 6.5, fmt.Sprintf("#%d", b.ID), "1", 0, "L", remplir, 0, "")
		pdf.CellFormat(largeurs[1], 6.5, c.t(b.Nom), "1", 0, "L", remplir, 0, "")
		pdf.CellFormat(largeurs[2], 6.5, c.t(b.Prenom), "1", 0, "L", remplir, 0, "")
		pdf.CellFormat(largeurs[3], 6.5, c.t(role), "1", 0, "L", remplir, 0, "")
		pdf.Ln(-1)
	}
	pdf.Ln(4)
}

func dessinerProduits(c contexte, doc DocumentRecap) {
	pdf := c.pdf
	titre := doc.TitreProduits
	if titre == "" {
		titre = "Produits"
	}
	dessinerTitreSection(c, fmt.Sprintf("%s (%d)", titre, len(doc.Produits)))

	if len(doc.Produits) == 0 {
		pdf.SetFont("Helvetica", "I", 9)
		pdf.SetTextColor(couleurTexteGris[0], couleurTexteGris[1], couleurTexteGris[2])
		pdf.CellFormat(0, 6, c.t("Aucun produit enregistré."), "", 1, "L", false, 0, "")
		pdf.SetTextColor(couleurTexteFonce[0], couleurTexteFonce[1], couleurTexteFonce[2])
		return
	}

	largeurs := []float64{15, 60, 65, 40}
	entetes := []string{"ID", "Nom", "Code-barres", "Qté"}
	const hauteurLigne = 18.0

	pdf.SetFont("Helvetica", "B", 9)
	pdf.SetFillColor(couleurBordure[0], couleurBordure[1], couleurBordure[2])
	for i, e := range entetes {
		align := "L"
		if e == "Qté" {
			align = "C"
		}
		pdf.CellFormat(largeurs[i], 7, c.t(e), "1", 0, align, true, 0, "")
	}
	pdf.Ln(-1)

	totalQte := 0
	pdf.SetFont("Helvetica", "", 9)
	for i, p := range doc.Produits {
		remplir := i%2 == 1
		if remplir {
			pdf.SetFillColor(couleurLigneAlt[0], couleurLigneAlt[1], couleurLigneAlt[2])
		}

		// Vérifie l'espace restant sur la page pour ne pas couper une ligne (et son
		// éventuelle image de code-barres) entre deux pages.
		if pdf.GetY()+hauteurLigne > 277 {
			pdf.AddPage()
		}

		pdf.CellFormat(largeurs[0], hauteurLigne, fmt.Sprintf("#%d", p.ID), "1", 0, "L", remplir, 0, "")
		pdf.CellFormat(largeurs[1], hauteurLigne, tronquer(c, p.Nom, largeurs[1]-4), "1", 0, "L", remplir, 0, "")

		xCode := pdf.GetX()
		yCode := pdf.GetY()
		pdf.CellFormat(largeurs[2], hauteurLigne, "", "1", 0, "C", remplir, 0, "")
		if img, err := genererImageCodeBarre(p.CodeBarre); err == nil {
			nomImg := fmt.Sprintf("barcode-%d", p.ID)
			opt := gofpdf.ImageOptions{ImageType: "PNG", ReadDpi: false}
			pdf.RegisterImageOptionsReader(nomImg, opt, bytes.NewReader(img))
			imgW := largeurs[2] - 8
			imgH := hauteurLigne - 8
			pdf.ImageOptions(nomImg, xCode+4, yCode+2, imgW, imgH, false, opt, 0, "")
			pdf.SetFont("Helvetica", "", 6)
			pdf.SetXY(xCode, yCode+hauteurLigne-5)
			pdf.CellFormat(largeurs[2], 4, p.CodeBarre, "", 0, "C", false, 0, "")
			pdf.SetFont("Helvetica", "", 9)
		} else if p.CodeBarre != "" {
			pdf.SetXY(xCode, yCode+hauteurLigne/2-2.5)
			pdf.CellFormat(largeurs[2], 5, c.t(p.CodeBarre), "", 0, "C", false, 0, "")
		} else {
			pdf.SetXY(xCode, yCode+hauteurLigne/2-2.5)
			pdf.CellFormat(largeurs[2], 5, c.t("—"), "", 0, "C", false, 0, "")
		}
		pdf.SetXY(xCode+largeurs[2], yCode)

		pdf.CellFormat(largeurs[3], hauteurLigne, fmt.Sprintf("%d", p.Quantite), "1", 0, "C", remplir, 0, "")
		pdf.Ln(-1)
		totalQte += p.Quantite
	}

	pdf.SetFont("Helvetica", "B", 9)
	pdf.SetFillColor(couleurBordure[0], couleurBordure[1], couleurBordure[2])
	pdf.CellFormat(largeurs[0]+largeurs[1]+largeurs[2], 7, c.t("Total"), "1", 0, "R", true, 0, "")
	pdf.CellFormat(largeurs[3], 7, fmt.Sprintf("%d", totalQte), "1", 0, "C", true, 0, "")
	pdf.Ln(-1)
}
