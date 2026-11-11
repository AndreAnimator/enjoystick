# Enjoystick: API da loja virtual

API REST em Laravel de uma loja virtual de controles e acessórios para jogos. Cobre a jornada completa de compra: catálogo público, carrinho de visitante, fusão do carrinho no login, checkout com baixa de estoque, pagamento simulado, acompanhamento de pedidos e uma área administrativa.

> Projeto final do curso. O pagamento é **simulado**: nenhum dado de cartão é solicitado, trafegado ou armazenado.

## Tecnologias

- PHP 8.4 (imagem Docker) e Laravel 12, no padrão **MVC + Service Layer**
- MySQL 8.4
- Laravel Sanctum (autenticação por sessão, com cookie)
- Docker e Docker Compose

Organização do código: os **Controllers** (`app/Http/Controllers/Api`) recebem a requisição e devolvem a resposta; as regras de carrinho, estoque e pedido ficam nos **Services** (`app/Services`); as entidades e relacionamentos ficam em `app/Models`; as respostas JSON são montadas pelos **Resources** (`app/Http/Resources`); a validação fica em `app/Http/Requests`.

## Como executar (Docker)

Pré-requisito: Docker com Docker Compose.

```bash
docker compose up --build
```

Na inicialização, o container `php` faz tudo sozinho, nesta ordem:

1. cria o `.env` a partir do `.env.example` (se ainda não existir) e gera a `APP_KEY`;
2. roda `composer install`;
3. roda `php artisan migrate`;
4. roda `php artisan db:seed` (só popula se o banco estiver vazio, então reiniciar não duplica dados);
5. sobe o servidor.

A primeira vez demora mais. Quando aparecer `Server running on [http://0.0.0.0:8000]`, está pronto.

| Serviço | Endereço |
| --- | --- |
| API | http://localhost/api |
| Health check | http://localhost/up |
| MySQL | `localhost:3306` (banco `enjoystick`, usuário `enjoystick`, senha `enjoystick`) |

Comandos úteis:

```bash
docker compose logs -f php                                  # acompanhar o log da API
docker compose exec php php artisan migrate:fresh --seed    # recriar o banco do zero
docker compose down -v                                      # parar e apagar o volume do banco
```

### Executar sem Docker (opcional)

Requer PHP 8.2 ou superior com as extensões `pdo_mysql`, `zip` e `bcmath` (os valores em dinheiro usam bcmath), Composer e um MySQL.

```bash
composer install
cp .env.example .env
php artisan key:generate
# ajuste as variáveis DB_* do .env para o seu MySQL (o padrão do .env.example é sqlite)
php artisan migrate --seed
php artisan serve
```

Se o frontend rodar em outra origem, inclua o domínio e a porta dele em `SANCTUM_STATEFUL_DOMAINS` (`.env`) e em `allowed_origins` (`config/cors.php`), para a sessão por cookie funcionar.

### Dockerfiles

- `Dockerfile.dev`: usado pelo `compose.yaml`. Inclui as dependências de desenvolvimento e o código é montado como volume.
- `Dockerfile`: produção. Instala só as dependências de produção, com autoloader otimizado e opcache. Ele **não** roda migrate nem seed: no deploy, execute `php artisan migrate --force`. Defina as variáveis `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` e `DB_*`.

## Credenciais dos seeders

Somente para desenvolvimento. Não use em produção.

| Papel | Nome | E-mail | Senha |
| --- | --- | --- | --- |
| Admin | Gestor | `gestorStock@enjoystick.com` | `controleSt0q` |
| Cliente | Adriano Vasconscelos | `adrivasco@gmail.com` | `V4sc0` |
| Cliente | Pedro Hercules | `ph.grego@outlook.com` | `ph123ph` |
| Cliente | André Miranda | `estupidezhonesta@proton.com` | `stupid123` |

O que os seeders criam:

- 4 categorias: Modified Controllers, Switches and Joysticks, Adaptive Controllers e Controllers;
- 32 produtos distribuídos entre elas, incluindo 1 sem estoque e 1 inativo, para testar as regras de indisponibilidade;
- 4 endereços de exemplo para os clientes (o Adriano tem dois);
- 3 carrinhos: um de **visitante** (o token dele é impresso no log do seed: `docker compose logs php | grep "Token do carrinho de visitante"`), um do Pedro e um do Adriano. O do Adriano inclui itens indisponíveis (um produto inativo e um sem estoque), para testar a sinalização no carrinho;
- 5 pedidos, um em cada status (`waiting`, `paid`, `sent`, `arrived` e `canceled`), com o estoque já descontado nos quatro que não foram cancelados.

Para testar a **fusão do carrinho**, faça login como Pedro enviando o token do visitante no header `X-Cart-Token`: um produto está nos dois carrinhos (as quantidades são somadas), outro passa do estoque (a quantidade é limitada ao estoque e vem um aviso em `warnings`) e um terceiro só existe no carrinho do visitante (é copiado).

## Autenticação e papéis

- Dois papéis: cliente (`client`) e administrador (`admin`).
- Autenticação por sessão com Sanctum: `POST /api/register`, `POST /api/login`, `POST /api/logout` e `GET /api/me`.
- Catálogo, detalhe do produto e carrinho de visitante são públicos. Checkout, pedidos, endereços e perfil exigem login.
- Criar, editar e excluir produtos e categorias, listar todos os pedidos e alterar o status de um pedido são restritos ao admin (rotas sob `/api/admin`). Um cliente que tenta fazer isso recebe **403**.

## Regras de negócio adotadas

### Carrinho

- O visitante tem um carrinho salvo no banco, identificado por um token aleatório de 64 caracteres. O frontend guarda o token e o envia no header `X-Cart-Token`.
- Um usuário logado tem no máximo um carrinho.
- Adicionar um produto que já está no carrinho soma a quantidade, sem duplicar a linha. Quantidade 0 remove o item.
- A quantidade é limitada ao estoque disponível. Produto inativo ou sem estoque não entra no carrinho.
- O carrinho **não congela preços**: ele sempre mostra o preço atual do produto. Subtotais e total são calculados no servidor.

### Fusão do carrinho no login e no registro

Regra adotada:

1. O frontend envia o `X-Cart-Token` do visitante no login ou registro.
2. A API funde esse carrinho com o carrinho da conta (que é criado se não existir).
3. Produto **só** no carrinho do visitante: é copiado para o carrinho da conta.
4. Produto **nos dois** carrinhos: as quantidades são somadas, limitadas ao estoque disponível. Se a soma passar do estoque, a quantidade é ajustada ao máximo disponível e o aviso volta no campo `warnings` da resposta.
5. Item do visitante com produto inativo ou sem estoque é descartado, também com aviso em `warnings`.
6. Concluída a fusão, o carrinho do visitante é apagado e o token dele deixa de valer. A resposta devolve o token do carrinho da conta no header `X-Cart-Token`.

### Checkout, estoque e pedido

- Exige login. Carrinho vazio ou inexistente é rejeitado com **422**.
- O frete é **fixo: R$ 10,00** por pedido.
- Tudo é calculado no servidor a partir dos preços gravados no banco; valores enviados pelo cliente são ignorados.
- A criação do pedido acontece em uma **transação**: valida o estoque, baixa o estoque, grava os itens e esvazia o carrinho. Se qualquer item falhar, nada é gravado.
- Estoque insuficiente (ou produto inativo) impede a finalização com **422**, informando o nome do produto que falhou.
- Cada item do pedido guarda o **preço unitário e o nome do produto** do momento da compra. Mudanças posteriores no catálogo não alteram pedidos já criados.
- O endereço de entrega é gravado no próprio pedido.

### Pagamento simulado, status e cancelamento

- `POST /api/orders/{id}/pay` marca o pedido como pago e registra a data e hora (`paid_at`). Só o dono do pedido (ou um admin) pode pagar, e só enquanto o pedido está aguardando pagamento.
- Status possíveis: `waiting` (aguardando pagamento) → `paid` (pago) → `sent` (enviado) → `arrived` (entregue). Também existe `canceled` (cancelado), apenas a partir de `waiting`.
- O admin altera o status em `PATCH /api/admin/orders/{id}/status`. Só transições válidas são aceitas; uma transição inválida retorna **409**.
- Cancelar um pedido (pelo cliente ou pelo admin) **devolve o estoque** dos itens.
- O cliente só vê os próprios pedidos; o admin vê todos e pode filtrar por status (`GET /api/admin/orders?status=paid`, com `waiting`, `paid`, `sent`, `arrived` ou `canceled`).

### Produtos e categorias

- **Excluir um produto o desativa** (`active = 0`); a linha continua no banco. Assim, pedidos e carrinhos antigos não quebram. Produtos inativos não aparecem no catálogo público nem podem ser comprados; o admin continua vendo e editando todos.
- **Uma categoria com produtos não pode ser excluída** (**409**). A contagem inclui os produtos inativos, porque eles continuam vinculados à categoria. Para excluí-la, o admin move os produtos para outra categoria antes.
- O campo `image` do produto guarda a URL da imagem (texto).

### Dinheiro e erros

- Valores monetários usam `decimal(10,2)` no banco e `bcmath` no PHP. Nunca ponto flutuante.
- Códigos HTTP usados: `401` (não autenticado), `403` (sem permissão), `404` (não encontrado), `409` (conflito de estado) e `422` (validação).

## Principais rotas

Todas com o prefixo `/api`.

| Rota | Descrição | Acesso |
| --- | --- | --- |
| `POST /register`, `POST /login` | Cria a conta / entra. Aceitam o `X-Cart-Token` para a fusão. | Público |
| `POST /logout`, `GET /me`, `PATCH /user` | Encerra a sessão / dados do usuário / edita o perfil | Autenticado |
| `GET /categories`, `GET /categories/{id}` | Lista e detalha categorias | Público |
| `GET /products` | Lista com paginação (4 por página, `?page=`). Parâmetros: `q` (busca por nome), `category` (id), `column` (`name`, `price` ou `created_at`) e `order` (`asc` ou `desc`) | Público |
| `GET /products/{id}` | Detalhe do produto (inativo retorna 404) | Público |
| `GET /cart` | Carrinho atual com subtotais e total | Público |
| `POST /cart/items` | Adiciona um item (soma se já existir) | Público |
| `PATCH /cart/items/{id}`, `DELETE /cart/items/{id}` | Altera a quantidade / remove o item | Público |
| `GET /addresses`, `POST /addresses` | Lista e cadastra endereços | Autenticado |
| `GET`, `PATCH`, `DELETE /addresses/{id}` | Detalha, edita e remove um endereço | Autenticado |
| `POST /orders` | Cria o pedido a partir do carrinho e baixa o estoque | Autenticado |
| `GET /orders`, `GET /orders/{id}` | Lista os pedidos / detalha um pedido | Dono ou admin |
| `POST /orders/{id}/pay` | Pagamento simulado | Dono ou admin |
| `POST /orders/{id}/cancel` | Cancela e devolve o estoque | Dono ou admin |
| `POST`, `PUT`, `DELETE /admin/products` | CRUD de produtos | Admin |
| `POST`, `PUT`, `DELETE /admin/categories` | CRUD de categorias | Admin |
| `GET /admin/orders` | Lista todos os pedidos da loja (filtro opcional `?status=`) | Admin |
| `PATCH /admin/orders/{id}/status` | Avança o status do pedido | Admin |