Notion Media Sync PHP

📌 Objetivo

Este projeto tem como objetivo baixar e armazenar imagens hospedadas no Notion, evitando problemas com URLs expiradas. O sistema recebe uma URL de imagem e a salva localmente em uma pasta no servidor, utilizando um identificador único como nome do arquivo.

🚀 Tecnologias Utilizadas

- PHP (Puro, sem frameworks, para simplicidade e compatibilidade com hospedagens compartilhadas)
- cURL (Para fazer o download das imagens)
- JSON (Para manipulação de dados e resposta da API)

✅ Requisitos

Antes de iniciar, certifique-se de que sua hospedagem possui:

- PHP 7.4 ou superior
- Extensão cURL habilitada
- Permissões de escrita na pasta de destino das imagens