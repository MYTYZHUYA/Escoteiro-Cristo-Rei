## Assuntos relacionados:
- [Endpoints](Endpoints.md)

# Formato das rotas:
```
[<Autenticada> <Método HTTP> <Endpoint>]
handler = <Classe>::<Método>
body = '{"<parameter_name>":"<type>"}' (opcional)
```

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