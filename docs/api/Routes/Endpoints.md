## Assuntos relacionados:
- [Rotas](Routes.md)

# Formato de Endpoints:
```
/<nome-do-endpoint>/<{algum_parâmetro}>
```

## Regras de criação de Endpoints:
- Um Endpoint deve sempre começar com "/"
- Um Endpoint pode ter nenhum ou vários parâmetros
- Parâmetros devem ser declarados entre chaves ({ })

## Como nomear um endpoint:
- A primeira parte de um endpoint (depois da /) deve ser o nome (ou apelido) do controlador responsável por ele

### Exemplo:
```
/accounts/login

(/Controlador/Endpoint)
```
