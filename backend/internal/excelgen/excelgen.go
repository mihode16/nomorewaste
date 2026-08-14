// Package excelgen construit le fichier Excel (.xlsx) du planning d'un bénévole :
// ses affectations (collectes, tournées, services) sur une période donnée.
package excelgen

import (
	"fmt"
	"time"

	"github.com/xuri/excelize/v2"
)

type LignePlanning struct {
	Date   time.Time
	Type   string // "Collecte", "Tournée" ou "Service"
	Detail string
	Statut string
}

func Generer(benevoleNom, periode string, lignes []LignePlanning) ([]byte, error) {
	f := excelize.NewFile()
	defer f.Close()

	sheet := "Planning"
	f.SetSheetName("Sheet1", sheet)
	f.DeleteSheet("Sheet1")
	f.NewSheet(sheet)
	f.SetActiveSheet(0)

	titreStyle, _ := f.NewStyle(&excelize.Style{
		Font:      &excelize.Font{Bold: true, Size: 16, Color: "FFFFFF"},
		Fill:      excelize.Fill{Type: "pattern", Color: []string{"198754"}, Pattern: 1},
		Alignment: &excelize.Alignment{Horizontal: "center", Vertical: "center"},
	})
	sousTitreStyle, _ := f.NewStyle(&excelize.Style{
		Font:      &excelize.Font{Italic: true, Size: 11, Color: "555555"},
		Alignment: &excelize.Alignment{Horizontal: "center"},
	})
	enteteStyle, _ := f.NewStyle(&excelize.Style{
		Font:      &excelize.Font{Bold: true, Color: "FFFFFF"},
		Fill:      excelize.Fill{Type: "pattern", Color: []string{"146c43"}, Pattern: 1},
		Alignment: &excelize.Alignment{Horizontal: "center", Vertical: "center"},
		Border: []excelize.Border{
			{Type: "left", Color: "0f5132", Style: 1},
			{Type: "top", Color: "0f5132", Style: 1},
			{Type: "bottom", Color: "0f5132", Style: 1},
			{Type: "right", Color: "0f5132", Style: 1},
		},
	})
	ligneStyle, _ := f.NewStyle(&excelize.Style{
		Border: []excelize.Border{
			{Type: "left", Color: "cccccc", Style: 1},
			{Type: "top", Color: "cccccc", Style: 1},
			{Type: "bottom", Color: "cccccc", Style: 1},
			{Type: "right", Color: "cccccc", Style: 1},
		},
	})
	ligneAltStyle, _ := f.NewStyle(&excelize.Style{
		Fill: excelize.Fill{Type: "pattern", Color: []string{"eaf6ec"}, Pattern: 1},
		Border: []excelize.Border{
			{Type: "left", Color: "cccccc", Style: 1},
			{Type: "top", Color: "cccccc", Style: 1},
			{Type: "bottom", Color: "cccccc", Style: 1},
			{Type: "right", Color: "cccccc", Style: 1},
		},
	})

	f.SetCellValue(sheet, "A1", "Planning de "+benevoleNom)
	f.MergeCell(sheet, "A1", "E1")
	f.SetCellStyle(sheet, "A1", "E1", titreStyle)
	f.SetRowHeight(sheet, 1, 28)

	f.SetCellValue(sheet, "A2", "Période : "+periode)
	f.MergeCell(sheet, "A2", "E2")
	f.SetCellStyle(sheet, "A2", "E2", sousTitreStyle)
	f.SetRowHeight(sheet, 2, 20)

	headerRow := 4
	headers := []string{"Date", "Heure", "Type", "Détail", "Statut"}
	for i, h := range headers {
		cell, _ := excelize.CoordinatesToCellName(i+1, headerRow)
		f.SetCellValue(sheet, cell, h)
	}
	debutHeader, _ := excelize.CoordinatesToCellName(1, headerRow)
	finHeader, _ := excelize.CoordinatesToCellName(len(headers), headerRow)
	f.SetCellStyle(sheet, debutHeader, finHeader, enteteStyle)
	f.SetRowHeight(sheet, headerRow, 22)

	row := headerRow + 1
	for i, l := range lignes {
		style := ligneStyle
		if i%2 == 1 {
			style = ligneAltStyle
		}
		f.SetCellValue(sheet, fmt.Sprintf("A%d", row), l.Date.Format("02/01/2006"))
		f.SetCellValue(sheet, fmt.Sprintf("B%d", row), l.Date.Format("15:04"))
		f.SetCellValue(sheet, fmt.Sprintf("C%d", row), l.Type)
		f.SetCellValue(sheet, fmt.Sprintf("D%d", row), l.Detail)
		f.SetCellValue(sheet, fmt.Sprintf("E%d", row), l.Statut)
		debut, _ := excelize.CoordinatesToCellName(1, row)
		fin, _ := excelize.CoordinatesToCellName(len(headers), row)
		f.SetCellStyle(sheet, debut, fin, style)
		row++
	}

	if len(lignes) == 0 {
		f.SetCellValue(sheet, fmt.Sprintf("A%d", row), "Aucune affectation sur cette période.")
		f.MergeCell(sheet, fmt.Sprintf("A%d", row), fmt.Sprintf("E%d", row))
		f.SetCellStyle(sheet, fmt.Sprintf("A%d", row), fmt.Sprintf("E%d", row), sousTitreStyle)
	}

	f.SetColWidth(sheet, "A", "A", 14)
	f.SetColWidth(sheet, "B", "B", 10)
	f.SetColWidth(sheet, "C", "C", 14)
	f.SetColWidth(sheet, "D", "D", 48)
	f.SetColWidth(sheet, "E", "E", 16)

	buf, err := f.WriteToBuffer()
	if err != nil {
		return nil, err
	}
	return buf.Bytes(), nil
}
