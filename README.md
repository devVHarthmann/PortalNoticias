# TechPortal — Portal de Notícias sobre Tecnologia & Inovação

Sistema completo de portal de notícias desenvolvido em PHP com MySQL, aplicando
conceitos de desenvolvimento web: CRUDs, autenticação, sessões e interface responsiva.

---

## Estrutura do Projeto

```
portal_noticias/
├── classes/
│   ├── Database.php          # Conexão PDO com MySQL
│   ├── Noticia.php           # CRUD de notícias
│   └── Usuario.php           # CRUD de usuários + login
├── imagens/                  # Upload de imagens das notícias
├── config.php                # Instancia a conexão com o banco
├── funcoes.php               # Funções auxiliares (resumo, formatarData, h…)
├── verifica_login.php        # Guard — redireciona se não estiver logado
├── style.css                 # Estilização (design editorial/jornalístico)
│
├── index.php                 # Página inicial — listagem pública de notícias
├── noticia.php               # Leitura individual de uma notícia
├── login.php                 # Formulário de login
├── cadastro.php              # Cadastro de novo usuário
├── logout.php                # Encerra a sessão
│
├── dashboard.php             # Painel do usuário logado
├── nova_noticia.php          # Formulário para nova notícia
├── editar_noticia.php        # Edição (somente pelo autor)
├── excluir_noticia.php       # Exclusão (somente pelo autor)
│
├── usuarios.php              # Lista de usuários cadastrados
├── editar_usuario.php        # Edição de conta (somente o próprio usuário)
├── excluir_usuario.php       # Exclusão de conta (somente o próprio usuário)
│
└── dump.sql                  # Estrutura e dados de exemplo do banco
```

---

## Requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior (ou MariaDB 10.3+)
- Servidor web: Apache ou Nginx com mod_rewrite habilitado
- Extensão PDO e PDO_MySQL ativas no PHP
- Extensão cURL ativa (para integração com OpenWeatherMap API)

---

## Instalação

### 1. Clone ou copie os arquivos
Coloque a pasta `portal_noticias/` dentro do diretório público do seu servidor
(ex: `htdocs/` no XAMPP ou `www/` no WAMP).

### 2. Crie o banco de dados
Acesse o phpMyAdmin ou o cliente MySQL de sua preferência e execute:

```sql
source /caminho/para/dump.sql
```

Ou cole o conteúdo do arquivo `dump.sql` diretamente no phpMyAdmin.

### 3. Configure a conexão
Edite o arquivo `classes/Database.php` com as credenciais do seu MySQL:

```php
private $host     = "localhost";
private $db_name  = "dbportalnoticias";
private $username = "root";
private $password = "";
```

### 4. Permissão de upload
Certifique-se de que a pasta `imagens/` tem permissão de escrita:

```bash
chmod 755 imagens/
```

### 5. Configuração da API de Clima (Opcional)
O widget de clima no hero da página inicial usa a OpenWeatherMap API. A chave já está configurada em `index.php`:

```php
define('OWM_API_KEY', 'e355c23ba8adfc8a397cfe9f8a9b71fb');
```

Se quiser usar uma chave própria:
1. Acesse [openweathermap.org](https://openweathermap.org/api)
2. Crie uma conta e obtenha sua chave gratuita
3. Substitua a chave em `index.php`

**Nota**: O sistema faz cache das requisições por 30 minutos, economizando chamadas à API.


---

## Conta de exemplo

| Campo | Valor           |
|-------|-----------------|
| Email | admin@portal.com |
| Senha | password        |

---

## Funcionalidades

### Página Inicial (Pública)
- **Widget de Clima em Tempo Real**: Exibe a temperatura de Sapucaia integrada com OpenWeatherMap API
  - Cache de 30 minutos usando `$_SESSION` para otimização
  - Atualiza automaticamente após expiração do cache
  - Fallback gracioso se a API falhar
- Listagem de todas as notícias publicadas
- Navegação intuitiva com header sticky

### Público (sem login)
- Listagem de todas as notícias na página inicial
- Leitura completa de uma notícia
- Cadastro de nova conta
- Login

### Usuário autenticado
- Publicar novas notícias com imagem de capa
- Editar e excluir **suas próprias** notícias
- Editar e excluir **sua própria** conta
- Ver lista de usuários cadastrados
- Dashboard com estatísticas pessoais

---

## Segurança implementada

- Senhas armazenadas com `password_hash(PASSWORD_BCRYPT)`
- Verificação com `password_verify()`
- Toda saída HTML sanitizada com `htmlspecialchars()`
- Prepared statements PDO em todas as queries (proteção contra SQL Injection)
- Verificação de autoria antes de editar/excluir notícias
- Guard `verifica_login.php` protegendo todas as páginas restritas
- Validação de tipo e tamanho no upload de imagens

---

## Design

O portal utiliza um tema **editorial/jornalístico** com:
- Tipografia: **Playfair Display** (títulos) + **Source Sans 3** (corpo) + **Source Serif 4** (leitura)
- Paleta: fundo creme (#F5F0E8), texto quase-preto (#1A1614), acento vermelho (#C0392B)
- Layout responsivo com CSS Grid e Flexbox
- Cards com hover animado, tabelas estilizadas, formulários refinados

### Responsividade
- Header adaptativo com navegação otimizada para mobile
- Grid de notícias se transforma de 3 colunas para 1 em telas pequenas
- Padding e gaps fluidos usando `clamp()` para transição suave entre breakpoints
- Widget de clima visível em telas maiores e oculto em mobile
