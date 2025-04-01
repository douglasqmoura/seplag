# 📘 Documentação da API - Cadastro de Servidores

Esta API foi desenvolvida para a gestão de servidores públicos, com cadastro e consulta de servidores efetivos e temporários, unidades, lotações e funcionalidades de autenticação.

Abaixo estão descritos os principais endpoints, regras de autenticação e exemplos de requisição.

---

## 🔐 Autenticação

### `POST /api/login`

Autentica um usuário e retorna um token de acesso gerenciado pelo Laravel Sanctum.

#### Body:

```json
{
    "email": "admin@seplag.mt.gov.br",
    "password": "senha123"
}
```

#### Response Esperada:

```json
{
    "token": "20|YDeY948TAdhSeZkvQ12ZnnC2XTPMPPR5n7WHvdgJab8853a7",
    "expires_at": "2025-04-01 20:58:30"
}
```

> ⚠️ O token tem validade de **5 minutos**. Caso ainda esteja válido, você pode renová-lo com o endpoint abaixo:

### `POST /api/refresh-token`

Renova a validade do token atual por mais 5 minutos (mantém o mesmo token ativo).

#### Headers:

-   `Authorization: Bearer {token}`
-   `Accept: application/json`

#### Response Esperada:

```json
{
    "token": "20|YDeY948TAdhSeZkvQ12ZnnC2XTPMPPR5n7WHvdgJab8853a7",
    "expires_at": "2025-04-01 21:03:30"
}
```

---

## 👥 Tipos de Usuário e Permissões

A API possui dois tipos de usuários:

| Tipo  | E-mail                 | Senha    | Permissões                            |
| ----- | ---------------------- | -------- | ------------------------------------- |
| Admin | admin@seplag.mt.gov.br | senha123 | Pode acessar e modificar (CRUD total) |
| Comum | guest@seplag.mt.gov.br | senha123 | Acesso somente leitura (GET)          |

⚠️ Apenas usuários do tipo **Admin** podem realizar operações de gravação: `POST`, `PUT`, `PATCH`, `DELETE`.

---

## 🏢 Unidades

### `GET /api/unidade`

Retorna a lista de unidades.

### `GET /api/unidade/{id}`

Retorna uma unidade específica.

### `POST /api/unidade`

Cria uma nova unidade.

#### Body:

```json
{
    "unid_nome": "Secretaria de Educação",
    "unid_sigla": "SEDUC",
    "end_tipo_logradouro": "Rua",
    "end_logradouro": "Av. Historiador Rubens de Mendonça",
    "end_numero": 1234,
    "end_bairro": "CPA",
    "cid_nome": "Cuiabá",
    "cid_uf": "MT"
}
```

### `PUT /api/unidade/{id}`

Atualiza os dados de uma unidade.

### `DELETE /api/unidade/{id}`

Remove uma unidade.

---

## 👤 Servidor Efetivo

### `GET /api/servidor-efetivo`

Lista todos os servidores efetivos.

### `GET /api/servidor-efetivo/{matricula}`

Busca um servidor pelo número de matrícula.

### `POST /api/servidor-efetivo`

Cria um novo servidor efetivo.

#### Body (multipart/form-data):

| Campo           | Tipo    | Obrigatório | Descrição                            |
| --------------- | ------- | ----------- | ------------------------------------ |
| matricula       | string  | sim         | Matrícula do servidor                |
| nome            | string  | sim         | Nome completo                        |
| data_nascimento | date    | sim         | Data de nascimento (YYYY-MM-DD)      |
| sexo            | string  | sim         | Masculino, Feminino ou Outro         |
| mae             | string  | sim         | Nome da mãe                          |
| pai             | string  | não         | Nome do pai                          |
| tipo_logradouro | string  | sim         | Tipo do logradouro (Rua, Avenida...) |
| logradouro      | string  | sim         | Nome da rua                          |
| numero          | integer | sim         | Número do endereço                   |
| bairro          | string  | sim         | Bairro                               |
| cidade          | string  | sim         | Cidade                               |
| uf              | string  | sim         | Unidade Federativa (ex: MT)          |
| fotos[]         | arquivo | não         | Imagens (um ou mais arquivos)        |

---

#### Body:

```json
{
    "matricula": "20250003",
    "nome": "Pedro Santos",
    "data_nascimento": "1978-07-20",
    "sexo": "Masculino",
    "mae": "Mariana Santos",
    "pai": "José Santos",
    "tipo_logradouro": "Rua",
    "logradouro": "Rua das Flores",
    "numero": 203,
    "bairro": "Jardim Imperial",
    "cidade": "Rondonópolis",
    "uf": "MT"
}
```

### `PUT /api/servidor-efetivo/{matricula}`

Atualiza os dados de um servidor efetivo existente.

#### Body (multipart/form-data):

| Campo           | Tipo    | Obrigatório | Descrição                             |
| --------------- | ------- | ----------- | ------------------------------------- |
| matricula       | string  | sim         | Matrícula do servidor                 |
| nome            | string  | sim         | Nome completo                         |
| data_nascimento | date    | sim         | Data de nascimento (YYYY-MM-DD)       |
| sexo            | string  | sim         | Masculino ou Feminino                 |
| mae             | string  | sim         | Nome da mãe                           |
| pai             | string  | não         | Nome do pai                           |
| tipo_logradouro | string  | sim         | Tipo do logradouro (Rua, Avenida...)  |
| logradouro      | string  | sim         | Nome da rua                           |
| numero          | integer | sim         | Número do endereço                    |
| bairro          | string  | sim         | Bairro                                |
| cidade          | string  | sim         | Cidade                                |
| uf              | string  | sim         | Unidade Federativa (ex: MT)           |
| fotos[]         | arquivo | não         | Novas imagens a serem adicionadas     |
| remover_fotos[] | integer | não         | IDs das fotos que devem ser removidas |

---

### `DELETE /api/servidor-efetivo/{matricula}`

Remove um servidor efetivo.

---

## 🕒 Servidor Temporário

### `GET /api/servidor-temporario`

Lista os servidores temporários.

### `GET /api/servidor-temporario/{id}`

Retorna os dados de um servidor temporário.

### `POST /api/servidor-temporario`

Cria um novo servidor temporário.

#### Body (multipart/form-data):

| Campo           | Tipo    | Descrição                       |
| --------------- | ------- | ------------------------------- |
| nome            | string  | Nome completo                   |
| data_nascimento | date    | Data de nascimento (YYYY-MM-DD) |
| sexo            | string  | Masculino/Feminino              |
| mae             | string  | Nome da mãe                     |
| pai             | string  | Nome do pai (opcional)          |
| tipo_logradouro | string  | Tipo do logradouro              |
| logradouro      | string  | Nome da rua/avenida             |
| numero          | integer | Número                          |
| bairro          | string  | Bairro                          |
| cidade          | string  | Cidade                          |
| uf              | string  | Estado (UF)                     |
| data_admissao   | date    | Data de admissão (YYYY-MM-DD)   |
| fotos[]         | arquivo | Upload de fotos (múltiplas)     |

---

Cria um novo servidor temporário.

### `PUT /api/servidor-temporario/{id}`

Atualiza um servidor temporário existente.

### `DELETE /api/servidor-temporario/{id}`

Remove um servidor temporário.

---

## 📌 Lotação

### `GET /api/lotacao`

Lista as lotações registradas.

### `GET /api/lotacao/{id}`

Retorna detalhes da lotação.

### `POST /api/lotacao`

Cria nova lotação.

#### Body (JSON):

```json
{
    "pes_id": 50,
    "unid_id": 12,
    "lot_data_lotacao": "2012-06-19",
    "lot_data_remocao": null,
    "lot_portaria": "001/2012"
}
```

---

Cria nova lotação.

### `PUT /api/lotacao/{id}`

Atualiza lotação.

---

## 🔍 Consultas

### `GET /api/consultas/servidores-efetivos/por-unidade/{id}`

Lista servidores efetivos lotados na unidade especificada.

### `GET /api/consultas/servidores-efetivos/endereco-funcional`

Consulta por nome com paginação:

```json
{
    "nome": "Doug",
    "per_page": 2
}
```

---

## 🔐 Headers obrigatórios (quando autenticado)

-   `Authorization: Bearer {token}`
-   `Accept: application/json`
-   `Content-Type: application/json` ou `multipart/form-data`------

## 📄 Paginação

Todas as listagens da API são paginadas por padrão com base em um helper customizado.

Você pode utilizar os seguintes parâmetros na query:

| Parâmetro  | Descrição                      | Padrão |
| ---------- | ------------------------------ | ------ |
| `per_page` | Quantidade de itens por página | 10     |
| `page`     | Número da página atual         | 1      |

O helper trata automaticamente tanto coleções em memória quanto queries do Eloquent, mantendo a estrutura esperada de paginação padrão do Laravel:

### Exemplo de resposta paginada:

```json
{
  "data": [ ... ],
  "links": {
    "first": "http://.../page=1",
    "last": "http://.../page=10",
    "prev": null,
    "next": "http://.../page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "path": "http://...",
    "per_page": 10,
    "to": 10,
    "total": 100
  }
}
```
