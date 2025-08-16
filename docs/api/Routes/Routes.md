## Assuntos relacionados:
- [Endpoints](Endpoints.md)

# Formato das rotas:
```
[<Requer Autenticação> <Método HTTP> <Endpoint>]
handler = <Classe>::<Método>
body = '{"<parameter_name>":"<type>"}' (opcional)
```

## Outros:
- Rotas por padrão não requerem autenticação, use "*" antes do método para definir que usa autenticação

## Exemplos:
Rota Simples:
```
[GET /get-routes]
handler = GeneralController::getRoutes
```

Rota com body definido:
```
[POST /accounts/login]
handler = AccountController::login
body = '{"username":"string","password":"string"}'
```

Rota que requer autenticação:

```
[* GET /users/{user_id}]
handler = UserController::getUser
```