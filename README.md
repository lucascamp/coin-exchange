# coin-exchange

`coin-exchange` é o backend da aplicação **Coin Exchange**. Ele fornece a interface de usuário para interação com o sistema de troca de moedas, permitindo que os usuários façam transações de forma intuitiva e eficiente.

Este repositório faz parte do projeto **coin-exchange](https://github.com/lucascamp/exchange-frontend)**, um sistema para troca de moedas.

## ⚡️ Funcionalidades

- Interface em tempo real para visualização das taxas de câmbio.
- Integração com a API backend para fazer transações.
- Suporte a funcionalidades avançadas como login e preferências do usuário.

# Requirementos
- Versão instável do [Docker](https://docs.docker.com/engine/install/)
- Versão compatível do [Docker Compose](https://docs.docker.com/compose/install/#install-compose)

## 🚀 Instalação e Configuração

Siga os passos abaixo para rodar o projeto localmente.

### 1. **Clone o Repositório**

Comece clonando o repositório para sua máquina local:

```sh
git clone https://github.com/lucascamp/coin-exchange.git
cd coin-exchange
```

### 2. **Como fazer o deploy a primeira vez**
Instale as dependências necessárias para rodar o projeto:

```sh
docker compose up -d --build
docker compose exec php bash
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
composer setup
php artisan migrate --seed
```
se quiser resetar o banco e repopular com os seeds
```sh
 docker compose exec php php artisan migrate:refresh --seed
 ```

para rodar novamente so executar
```sh
docker compose up -d
``` 

### Curl`s

Login 

Essa request retorna o api token que deve ser usados nas outras requests

Os usuários são: lucas@test.com, leonardo@test.com, jose@test.com todos com a senha 123456
```sh
curl --request POST \
  --url 'http://localhost/api/login?=' \
  --header 'Content-Type: application/json' \
  --header 'User-Agent: insomnia/10.2.0' \
  --data '{
	"email": "lucas@test.com",
	"password": "123456"
}'
``` 
Logout
```sh
curl --request POST \
  --url 'http://localhost/api/logout/1?=' \
  --header 'Authorization: Bearer {TOKEN_AQUI}' \
  --header 'Content-Type: application/json' \
  --header 'User-Agent: insomnia/10.2.0'
``` 
List Coins
```sh
curl --request GET \
  --url http://localhost/api/coin \
  --header 'Authorization: Bearer {TOKEN_AQUI}' \
  --header 'User-Agent: insomnia/10.2.0'
```

List Transactions
```sh
curl --request GET \
  --url http://localhost/api/listTransactions \
  --header 'Authorization: Bearer {TOKEN_AQUI}' \
  --header 'User-Agent: insomnia/10.2.0'
```
Exchange coins

Basta passar na URL qual a moeda, USD(dolar), CAN(dolar canadense), EUR(euro)
e o valor na propriedade amount de quantos reais (BRL) serão convertidos.

```sh
curl --request POST \
  --url http://localhost/api/exchange/{MOEDA_AQUI} \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer {TOKEN_AQUI}' \
  --header 'Content-Type: application/json' \
  --header 'User-Agent: insomnia/10.2.0' \
  --data '{
  "amount":100
}'
