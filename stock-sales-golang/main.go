package main

import (
	"fmt"
	"net/http"
)

func homePage(w http.ResponseWriter, r *http.Request) {
	fmt.Fprintf(w, "Bem-vindo ao meu website em Go!")
}

func main() {
	http.HandleFunc("/", homePage)
	fmt.Println("Servidor rodando na porta 5000")
	http.ListenAndServe(":5000", nil)
}
