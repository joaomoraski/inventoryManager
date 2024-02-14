package http_server

import (
	"log"
	"net/http"
)

func main() {
	handler := http.HandlerFunc(HttpServer)
	if err := http.ListenAndServe(":5000", handler); err != nil {
		log.Fatalf("não foi possivel ouvir a chama na porta 5000 %v", err)
	}
}
