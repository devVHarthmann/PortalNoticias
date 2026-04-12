-- ============================================
-- Portal de Notícias - Estrutura do Banco
-- ============================================

CREATE DATABASE IF NOT EXISTS dbportalnoticias
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE dbportalnoticias;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nome      VARCHAR(100)  NOT NULL,
  email     VARCHAR(150)  NOT NULL UNIQUE,
  telefone  VARCHAR(20)   DEFAULT NULL,
  senha     VARCHAR(255)  NOT NULL,
  is_admin  TINYINT(1)    NOT NULL DEFAULT 0,
  criado_em DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Notícias
CREATE TABLE IF NOT EXISTS noticias (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  titulo    VARCHAR(255)  NOT NULL,
  noticia   LONGTEXT      NOT NULL,
  data      DATE          NOT NULL,
  autor     INT           NOT NULL,
  imagem    VARCHAR(255)  DEFAULT NULL,
  criado_em DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_autor FOREIGN KEY (autor) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuário admin (senha: password)
INSERT INTO usuarios (nome, email, telefone, senha, is_admin) VALUES
('Admin Portal', 'admin@portal.com', '(51) 99999-0000',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Notícias de exemplo
INSERT INTO noticias (titulo, noticia, data, autor, imagem) VALUES
('Inteligência Artificial revoluciona o mercado de trabalho',
 'A inteligência artificial está transformando radicalmente o mercado de trabalho em todo o mundo. Empresas de diversos setores estão adotando soluções baseadas em IA para automatizar tarefas repetitivas, aumentar a produtividade e reduzir custos operacionais. Especialistas apontam que, até 2030, mais de 85 milhões de empregos poderão ser substituídos por máquinas, mas ao mesmo tempo cerca de 97 milhões de novos postos de trabalho devem surgir. A chave para os profissionais do futuro será a capacidade de trabalhar em conjunto com sistemas inteligentes, desenvolvendo habilidades como pensamento crítico, criatividade e inteligência emocional.',
 CURDATE(), 1, NULL),
('Novo chip quântico da Google promete computação 1 milhão de vezes mais rápida',
 'A Google anunciou um avanço sem precedentes no campo da computação quântica com o lançamento do seu novo processador Willow. O chip é capaz de realizar cálculos em menos de 5 minutos que levariam 10 septilhões de anos nos computadores clássicos mais avançados. Este feito representa um marco histórico para a ciência da computação e abre caminho para aplicações práticas em áreas como descoberta de medicamentos, modelagem climática e criptografia.',
 CURDATE(), 1, NULL),
('Brasil lidera ranking de startups de tecnologia verde na América Latina',
 'O Brasil consolidou sua posição como líder em inovação tecnológica sustentável na América Latina, segundo relatório divulgado pela associação internacional de venture capital. O país conta hoje com mais de 1.200 startups focadas em tecnologia verde, atuando em segmentos como energia solar, gestão de resíduos, agrotecnologia sustentável e mobilidade elétrica.',
 CURDATE(), 1, NULL);

-- Para bancos já existentes: adicione a coluna is_admin se ainda não existir
-- ALTER TABLE usuarios ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0;
-- UPDATE usuarios SET is_admin = 1 WHERE email = 'admin@portal.com';
