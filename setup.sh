#!/bin/bash

# Verifica se o .env já existe
if [ ! -f .env ]; then
  echo "Criando .env a partir de .env.example"
  cp .env.example .env
else
  echo ".env já existe, pulando criação"
fi

# Sobe os containers
echo "Subindo containers com Docker Compose..."
docker-compose up -d

# Aguarda um tempo para garantir que os containers estejam prontos
echo "Aguardando containers iniciarem..."
sleep 10

# Instala as dependências
echo "Instalando dependências PHP..."
docker-compose exec php bash -c "composer install"

# Pergunta ao usuário sobre as seeds
echo ""
read -p "Executar todas as seeds para gerar dados de exemplo? (s/N): " resposta

# Converte para minúsculo
resposta=$(echo "$resposta" | tr '[:upper:]' '[:lower:]')

if [[ "$resposta" == "s" || "$resposta" == "sim" ]]; then
  echo "Executando migrations e todas as seeds..."
  docker-compose exec php bash -c "php artisan migrate:fresh --seed"
else
  echo "Executando migrations e apenas a UserSeeder..."
  docker-compose exec php bash -c "php artisan migrate:fresh && php artisan db:seed --class=UserSeeder"
fi

echo ""
echo "✅ Setup concluído com sucesso!"
