# Adquirência Challenge API

Este projeto é uma API em Laravel para criação de transações Pix, solicitação de saques (withdraw) e processamento de webhooks de confirmação, utilizando filas para processamento assíncrono e PostgreSQL como banco de dados (com Docker). Inclui autenticação via Sanctum, validações, e um fluxo de webhook resiliente.

## Sumário
- Visão geral da arquitetura
- Pré-requisitos
- Configuração do ambiente
- Banco de dados PostgreSQL com Docker
- Instalação de dependências
- Migrações e Seeders
- Executando a aplicação
- Filas (Queue) e processamento de jobs
- Rotas e exemplos de requisição
- Webhooks e fluxo de processamento
- Padrões e ideias de arquitetura
- Troubleshooting

---

## Visão Geral da Arquitetura

- **Laravel + Sanctum**: autenticação por token para proteger as operações de criação.
- **Controllers**: endpoints HTTP e orquestração de fluxo (Pix/Withdraw/Webhooks).
- **Requests**: validação de dados de entrada.
- **Jobs**: processamento assíncrono de eventos e webhooks (`PixWebhookJob`, `WithdrawWebhookJob`).
- **Models**: persistência de entidades (ex.: `PixTransaction`, `Withdraw`, `Subadquirente`, `User`).
- **Enums**: estados da transação (`PixTransactionStatus`).
- **Middleware `adquirencia`**: garante contexto/credenciais para subadquirentes.
- **Queues**: filas para processar webhooks e tarefas pesadas sem bloquear requisições.
- **Providers/Services**: integração com serviços de adquirência em `app/Services/Adquirencia`.

Fluxo resumido:
1. Cliente autentica e obtém token (`/api/login`).
2. Cliente cria uma transação Pix ou um pedido de saque (`/api/pix/create`, `/api/withdraw`).
3. A adquirência envia confirmação via webhook (`/api/webhook/pix`, `/api/webhook/withdraw`).
4. Webhooks disparam Jobs que atualizam estados e persistem dados, tudo via fila.

---

## Pré-requisitos

- Docker Desktop (Windows)
- PHP 8.2+
- Composer 2+

---

## Configuração do Ambiente

1. Copie o `.env.example` para `.env`:

```cmd
copy .env.example .env
```

2. Atualize variáveis no `.env` conforme seu ambiente:

- `APP_URL=http://localhost`
- `DB_CONNECTION=pgsql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=5432`
- `DB_DATABASE=adquirencia`
- `DB_USERNAME=postgres`
- `DB_PASSWORD=postgres`
- `QUEUE_CONNECTION=database` (recomendado para início)
- `SANCTUM_STATEFUL_DOMAINS=localhost`

3. Gere a chave da aplicação:

```cmd
php artisan key:generate
```

---

## Banco de Dados PostgreSQL com Docker

Há dois modos comuns de subir o PostgreSQL com Docker:

1) Ambiente padrão do projeto (banco `adquirencia`, usuário `postgres`):

```cmd
docker run -d -p 5432:5432 --name adquirencia-postgres -e POSTGRES_DB=adquirencia -e POSTGRES_USER=postgres -e POSTGRES_PASSWORD=postgres -v %cd%\postgres-data:/var/lib/postgresql/data postgres:16
```

2) Ambiente solicitado (associado ao comando fornecido, banco `laravel`, usuário `laravel`, senha `secret`):

```cmd
docker run --name postgres -e POSTGRES_DB=laravel -e POSTGRES_USER=laravel -e POSTGRES_PASSWORD=secret -p 5432:5432 -d postgres:16
```

Ajuste o `.env` conforme o ambiente escolhido:

- Para o modo 1:
  - `DB_DATABASE=adquirencia`
  - `DB_USERNAME=postgres`
  - `DB_PASSWORD=postgres`

- Para o modo 2:
  - `DB_DATABASE=laravel`
  - `DB_USERNAME=laravel`
  - `DB_PASSWORD=secret`

- Em ambos: `DB_HOST=127.0.0.1`, `DB_PORT=5432`.

Utilidades:

```cmd
:: verificar logs
docker logs adquirencia-postgres

:: parar e iniciar (modo 1)
docker stop adquirencia-postgres
docker start adquirencia-postgres

:: parar e iniciar (modo 2)
docker stop postgres
docker start postgres
```

---

## Instalação de Dependências

Dentro da pasta `api`:

```cmd
composer install
```

---

## Migrações e Seeders

Execute as migrações com seeders:

```cmd
php artisan migrate --seed
```

As migrações relevantes incluem tabelas de usuários, cache, jobs, subadquirentes, relacionamento usuário-subadquirente e transações Pix (ver `database/migrations/`).

---

## Executando a Aplicação

Suba o servidor HTTP:

```cmd
php artisan serve
```

Servidor padrão: `http://127.0.0.1:8000`.

---

## Filas (Queue) e Processamento de Jobs

O projeto usa filas para processamento dos webhooks e tarefas assíncronas. Configure no `.env`:

```
QUEUE_CONNECTION=database
```

Crie a tabela de jobs (já coberto por migrações) e inicie o worker:

```cmd
php artisan queue:work
```
---

## Rotas e Exemplos de Requisição

Arquivo: `routes/api.php`

- `POST /api/login` — autenticação, retorna token Sanctum.
- Protegidas por `auth:sanctum` e `adquirencia`:
  - `POST /api/pix/create` — cria transação Pix.
  - `POST /api/withdraw` — cria solicitação de saque.
  - `GET /api/user` — retorna usuário autenticado.
- Webhooks (não autenticados, protegidos por validação e assinatura conforme implementação):
  - `POST /api/webhook/pix` — processamento de eventos Pix.
  - `POST /api/webhook/withdraw` — processamento de eventos de saque.

### Login

```cmd
curl -X POST http://127.0.0.1:8000/api/login -H "Content-Type: application/json" -d "{\"email\":\"usuario@example.com\",\"password\":\"senha\"}"
```

Resposta esperada: token para usar em `Authorization: Bearer <token>`.

### Criar Pix

```cmd
curl -X POST http://127.0.0.1:8000/api/pix/create -H "Authorization: Bearer <token>" -H "Content-Type: application/json" -d "{\"amount\":100.50,\"description\":\"Pedido 123\"}"
```

### Solicitar Saque

```cmd
curl -X POST http://127.0.0.1:8000/api/withdraw -H "Authorization: Bearer <token>" -H "Content-Type: application/json" -d "{\"amount\":100.50,\"bank_account\":\"0001-12345-6\"}"
```

### Webhook Pix

```cmd
curl -X POST http://127.0.0.1:8000/api/webhook/pix -H "Content-Type: application/json" -d "{\"transaction_id\":\"abc123\",\"status\":\"CONFIRMED\",\"amount\":100.50}"
```

### Webhook Withdraw

```cmd
curl -X POST http://127.0.0.1:8000/api/webhook/withdraw -H "Content-Type: application/json" -d "{\"withdraw_id\":\"w123\",\"status\":\"PAID\",\"amount\":100.50}"
```

Observação: os formatos exatos de payload dependem dos `Requests` e `Controllers` do projeto. Ajuste conforme contratos definidos.
Observação 2: Use tambem o dump do insomnia.

---

## Webhooks e Fluxo de Processamento

- Ao receber um webhook, os controllers `WebhookPixController` e `WebhookWithdrawController` validam o payload e disparam os respectivos Jobs (`PixWebhookJob`, `WithdrawWebhookJob`).
- Os Jobs atualizam estados das entidades (`PixTransactionStatus`), registram logs, persistem alterações e podem acionar serviços em `app/Services/Adquirencia`.
- O uso de filas garante resiliência, retries e não bloqueio da API.

Estratégias comuns implementadas/esperadas:
- Idempotência via chaves únicas (ex.: `transaction_id`) para evitar duplicidade.
- Validação de assinatura do provedor (se disponível) para aceitar o webhook.
- Atualização de status e histórico de eventos.

---

## Padrões e Ideias de Arquitetura

- **Camadas claras**: Controllers (interface HTTP), Requests (validação), Jobs (processamento assíncrono), Models (domínio e persistência), Services (integrações externas), Providers (injeção/configuração), Enums (estados e regras de transição).
- **Fila-first**: tudo que pode demorar ou precisa de retries vai para fila.
- **Idempotência**: webhooks e criação de transações devem ser idempotentes.
- **Single-responsibility (S do SOLID)**: cada Controller, Request e Job tem uma responsabilidade única.
- **Open/Closed (O do SOLID)**: serviços de adquirência podem ser estendidos sem modificar código existente.
- **Liskov, Interface Segregation, Dependency Inversion (L/I/D do SOLID)**: dependências são injetadas via Providers e contratos, controllers dependem de interfaces.
- **Strategy Pattern**: integração com adquirência utiliza estratégias intercambiáveis em `app/Services/Adquirencia` (ex.: diferentes provedores Pix/Withdraw implementam uma mesma interface/contrato).
- **Resolvers**: Providers/Middlewares resolvem implementações em tempo de execução (via Service Container) com base no subadquirente e contexto de requisição.
- **Middleware de contexto**: `adquirencia` garante o vínculo/subadquirente correto.
- **Observabilidade**: logs em `storage/logs`, status via DB e eventos.

Estrutura-chave no código:
- `app/Jobs/PixJob.php`, `app/Jobs/PixWebhookJob.php`, `app/Jobs/WithdrawWebhookJob.php` — processamento assíncrono.
- `app/Models/PixTransaction.php`, `app/Models/Withdraw.php`, `app/Models/Subadquirente.php`, `app/Models/User.php` — modelos e relacionamentos.
- `app/Enums/PixTransactionStatus.php` — ciclo de vida de transações Pix.
- `app/Services/Adquirencia/*` — integração com provedores de pagamento baseada em Strategy e resolvida via Service Container.
- `routes/api.php` — mapeamento de endpoints.

---

## Troubleshooting

- **Erro de conexão no DB**: verifique `DB_*` no `.env`, container do Docker ativo e porta `5432` livre.
- **Migrações falhando**: rode `php artisan migrate:status`, inspecione migrations em `database/migrations` e cheque permissões do usuário PostgreSQL.
- **Queue não processa**: certifique-se de `QUEUE_CONNECTION=database`, `php artisan queue:work` está ativo, e que a tabela `jobs` existe.
- **Token inválido**: confirme login e header `Authorization: Bearer <token>`.
- **Webhooks ignorados**: valide o payload; confira logs em `storage/logs/laravel.log`.

---

## Comandos Úteis (Windows CMD)

```cmd
:: subir postgres
docker start adquirencia-postgres

:: instalar deps
composer install

:: gerar chave
php artisan key:generate

:: migrar
php artisan migrate

:: seed (opcional)
php artisan db:seed

:: rodar servidor
php artisan serve

:: iniciar filas
php artisan queue:work
```
