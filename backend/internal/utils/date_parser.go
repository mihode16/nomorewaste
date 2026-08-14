package utils

import "time"

// ParseDateTime accepte plusieurs formats de date/heure
func ParseDateTime(s string) (time.Time, error) {
	formats := []string{
		"2006-01-02 15:04:05",
		"2006-01-02T15:04:05",
		"2006-01-02T15:04",
		"2006-01-02 15:04",
		"2006-01-02",
	}
	var err error
	for _, f := range formats {
		var t time.Time
		t, err = time.ParseInLocation(f, s, time.Local)
		if err == nil {
			return t, nil
		}
	}
	return time.Time{}, err
}

// ParseDate parse une date au format YYYY-MM-DD dans le fuseau horaire local
// pour éviter le décalage d'un jour.
func ParseDate(s string) (time.Time, error) {
	loc := time.Local
	return time.ParseInLocation("2006-01-02", s, loc)
}