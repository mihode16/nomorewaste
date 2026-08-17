# Image de l'API Go (backend). Build multi-étapes : compilation dans un conteneur avec la
# toolchain Go complète, puis binaire seul copié dans une image minimale pour la production.

FROM golang:1.25-alpine AS builder

WORKDIR /app

COPY backend/go.mod backend/go.sum ./
RUN go mod download

COPY backend/ .

RUN CGO_ENABLED=0 GOOS=linux go build -o /api-server ./cmd/api

FROM alpine:3.20

RUN apk add --no-cache ca-certificates tzdata
ENV TZ=Europe/Paris

WORKDIR /app

COPY --from=builder /api-server ./api-server
# Logo utilisé dans les PDF récapitulatifs (LOGO_PATH=/assets/logo.png)
COPY assets/ /assets/

RUN mkdir -p storage/pdfs storage/plannings

EXPOSE 8081

CMD ["./api-server"]
