## Visão geral

Livros Viajantes é uma aplicação web PHP para troca, doação e empréstimo de livros entre jovens. O projeto utiliza uma arquitetura simples de MVC customizada, com rotas REST na API e autenticação via JWT.

## Tecnologias principais

- PHP 8+ (uso de namespaces e tipagem básica)
- Composer para autoload e dependências
- PostgreSQL para persistência de dados
- Firebase JWT para autenticação baseada em token
- Monolog para logging
- Dotenv para carregar variáveis de ambiente

## Estrutura do projeto

- `app/` - código fonte principal
  - `Bootstrap.php` - inicialização do ambiente, carregamento do `.env` e definição de constantes
  - `Controllers/` - lógica de tratamento de solicitações HTTP
  - `Models/` - manipulação de dados e consultas ao banco
  - `Database/` - conexão PDO singleton com PostgreSQL
  - `Utils/` - utilitários da aplicação
- `public/` - ponto de entrada público
  - `index.php` - front controller da aplicação
  - `api/router.php` - roteamento das APIs REST
- `database/` - scripts SQL úteis para criação de tabelas
- `vendor/` - dependências gerenciadas pelo Composer
- `README.md` - descrição geral do projeto

## Dependências Composer

- `vlucas/phpdotenv` - leitura de variáveis de ambiente
- `monolog/monolog` - logging da aplicação
- `firebase/php-jwt` - geração e validação de JWT

## Endpoints principais da API

### Usuário

- `POST /api/cadastrar` - cria conta de usuário
- `POST /api/login` - autentica e recebe token JWT
- `GET /api/perfil` - retorna dados do perfil do usuário autenticado
- `PUT /api/atualizar` - atualiza dados do perfil
- `DELETE /api/deletar` - remove conta do usuário

### Livros

- `GET /api/listar` - lista todos os livros públicos
- `GET /api/meus-livros` - lista livros do usuário autenticado
- `POST /api/publicar` - cria publicação de livro
- `GET /api/livros/{id}` - busca livro por ID
- `PUT /api/livros/{id}` - atualiza livro do autor autenticado
- `DELETE /api/livros/{id}` - remove livro do autor autenticado

### Solicitações

- `POST /api/solicitacoes` - cria interesse por livro
- `GET /api/solicitacoes` - lista solicitações do usuário
- `GET /api/solicitacoes/recebidas` - solicitações recebidas
- `GET /api/solicitacoes/enviadas` - solicitações enviadas
- `PUT /api/solicitacoes/{id}/responder` - responde solicitação
- `DELETE /api/solicitacoes/{id}/cancelar` - cancela solicitação

### Mensagens

- `GET /api/conversas` - lista conversas do usuário
- `GET /api/conversas/{id}` - lista mensagens com outro usuário
- `POST /api/conversas/{id}/mensagens` - envia mensagem para outro usuário
- `GET /api/usuarios/{id}` - busca dados de usuário por ID

## Mais informações:

- A autenticação usa JWT com `Authorization: Bearer <token>`.
- O sistema de upload de imagem do usuário aceita `jpg`, `jpeg`, `png`, `webp` até 5MB.
- A exclusão de livros é implementada como soft delete (`status = false`).
- A conexão com o banco está em `app/Database/Connection.php` e usa PDO diretamente.