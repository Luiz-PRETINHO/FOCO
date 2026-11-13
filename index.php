<?php
session_start();
require "conexao.php";

// ==========================================
// LÓGICA DO SISTEMA
// ==========================================
if (isset($_POST['acao']) && $_POST['acao'] === 'login_prof') {
    $nome = trim($_POST['nome']); $senha = $_POST['senha'];
    $stmt = $conn->prepare("SELECT id, nome FROM professores WHERE nome = ?");
    $stmt->bind_param("s", $nome); $stmt->execute(); $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $stmt2 = $conn->prepare("SELECT id, nome FROM professores WHERE id = ? AND senha = ?");
        $stmt2->bind_param("is", $row['id'], $senha); $stmt2->execute(); $res2 = $stmt2->get_result();
        if ($row2 = $res2->fetch_assoc()) {
            $_SESSION['usuario'] = ['tipo' => 'prof', 'id' => $row2['id'], 'nome' => $row2['nome']];
            header("Location: index.php"); exit;
        } else { $erro_prof = "Senha incorreta para este professor."; }
    } else {
        $stmt = $conn->prepare("INSERT INTO professores (nome, senha) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $senha); $stmt->execute(); $id = $conn->insert_id;
        $_SESSION['usuario'] = ['tipo' => 'prof', 'id' => $id, 'nome' => $nome];
        header("Location: index.php"); exit;
    }
}

if (isset($_POST['acao']) && $_POST['acao'] === 'login_aluno') {
    $nome = trim($_POST['nome']); $matricula = trim($_POST['matricula']); $anoAtual = date('Y');
    if (!preg_match('/^' . $anoAtual . '\d{5}$/', $matricula)) {
        $erro_aluno = "Matrícula inválida. Use 9 dígitos começando com $anoAtual.";
    } else {
        $stmt = $conn->prepare("SELECT id, nome FROM alunos WHERE matricula = ?");
        $stmt->bind_param("s", $matricula); $stmt->execute(); $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $_SESSION['usuario'] = ['tipo' => 'aluno', 'id' => $row['id'], 'nome' => $row['nome']];
            header("Location: index.php"); exit;
        } else {
            $stmt = $conn->prepare("INSERT INTO alunos (nome, matricula) VALUES (?, ?)");
            $stmt->bind_param("ss", $nome, $matricula); $stmt->execute(); $id = $conn->insert_id;
            $_SESSION['usuario'] = ['tipo' => 'aluno', 'id' => $id, 'nome' => $nome];
            header("Location: index.php"); exit;
        }
    }
}

if (isset($_GET['sair'])) { session_destroy(); header("Location: index.php"); exit; }

if (isset($_POST['acao']) && $_POST['acao'] === 'criar_atividade') {
    $prof_id = $_SESSION['usuario']['id']; $materia = $_POST['materia']; $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo']; 
    
    $stmt = $conn->prepare("INSERT INTO atividades (professor_id, materia, titulo, conteudo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $prof_id, $materia, $titulo, $conteudo);
    $stmt->execute(); $atividade_id = $conn->insert_id;
    
    if (isset($_POST['pergunta']) && is_array($_POST['pergunta'])) {
        for ($i = 0; $i < count($_POST['pergunta']); $i++) {
            if (!empty($_POST['pergunta'][$i])) {
                $enunciado = $_POST['pergunta'][$i];
                $alternativas = json_encode(array_map('trim', explode(',', $_POST['opcoes'][$i])), JSON_UNESCAPED_UNICODE);
                $correta = (int)$_POST['correta'][$i] - 1;
                $stmt2 = $conn->prepare("INSERT INTO perguntas (atividade_id, enunciado, alternativas, correta) VALUES (?, ?, ?, ?)");
                $stmt2->bind_param("issi", $atividade_id, $enunciado, $alternativas, $correta); $stmt2->execute();
            }
        }
    }
    header("Location: index.php"); exit;
}

if (isset($_GET['excluir_ativ'])) {
    $id = (int)$_GET['excluir_ativ']; $conn->query("DELETE FROM atividades WHERE id = $id"); header("Location: index.php"); exit;
}

if (isset($_POST['acao']) && $_POST['acao'] === 'salvar_progresso') {
    $aluno_id = $_SESSION['usuario']['id']; $ativ_id = $_POST['atividade_id']; $acertos = $_POST['acertos']; $total = $_POST['total'];
    $stmt = $conn->prepare("INSERT INTO progresso (aluno_id, atividade_id, acertos, total_perguntas) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE acertos = GREATEST(acertos, VALUES(acertos))");
    $stmt->bind_param("iiii", $aluno_id, $ativ_id, $acertos, $total); $stmt->execute(); header("Location: index.php"); exit;
}

// BUSCA DE DADOS
$atividades = []; $alunos = []; $concluidas = []; $total_geral = 0;

if (isset($_SESSION['usuario'])) {
    if ($_SESSION['usuario']['tipo'] === 'prof') {
        $res = $conn->query("SELECT * FROM atividades ORDER BY id DESC");
        while ($row = $res->fetch_assoc()) {
            $stmt = $conn->prepare("SELECT * FROM perguntas WHERE atividade_id = ?"); $stmt->bind_param("i", $row['id']); $stmt->execute(); $res_p = $stmt->get_result();
            $row['perguntas'] = [];
            while ($p = $res_p->fetch_assoc()) { $p['opcoes_array'] = json_decode($p['alternativas'], true); $row['perguntas'][] = $p; }
            $atividades[] = $row;
        }
        $res = $conn->query("SELECT a.nome, a.matricula, COUNT(p.id) AS feitas FROM alunos a LEFT JOIN progresso p ON a.id = p.aluno_id GROUP BY a.id ORDER BY feitas DESC");
        while ($row = $res->fetch_assoc()) $alunos[] = $row;
    } else {
        $aluno_id = $_SESSION['usuario']['id'];
        $res = $conn->query("SELECT * FROM atividades ORDER BY materia, id DESC");
        while ($row = $res->fetch_assoc()) {
            $stmt = $conn->prepare("SELECT * FROM perguntas WHERE atividade_id = ?"); $stmt->bind_param("i", $row['id']); $stmt->execute(); $res_p = $stmt->get_result();
            $row['perguntas'] = [];
            while ($p = $res_p->fetch_assoc()) { $p['opcoes_array'] = json_decode($p['alternativas'], true); $row['perguntas'][] = $p; }
            $atividades[] = $row;
        }
        $res = $conn->query("SELECT atividade_id FROM progresso WHERE aluno_id = $aluno_id");
        while ($row = $res->fetch_assoc()) $concluidas[] = $row['atividade_id'];
        $total_geral = count($atividades);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FOCO — Aprender sem se perder</title>
<link rel="stylesheet" href="estilo.css"> <!-- LINK PARA O CSS SEPARADO -->
</head>
<body>

<?php if (!isset($_SESSION['usuario'])): ?>
<div id="login">
  <div class="login-card">
    <div class="logo-container">
      <!-- LOGO ATUALIZADA COM QUEBRA-CACHE -->
      <img src="img/logo.png?v=<?= time() ?>" class="logo-svg" alt="Logo Foco">
    </div>
    <p class="tagline">Aprender sem se perder</p>
    <div class="tabs">
      <button id="btn-tab-prof" class="ativo" onclick="trocarTab('prof')">Professor</button>
      <button id="btn-tab-aluno" onclick="trocarTab('aluno')">Aluno</button>
    </div>
    <div id="form-prof">
      <form method="POST">
        <input type="hidden" name="acao" value="login_prof">
        <div class="campo"><label>Nome</label><input name="nome" required placeholder="Seu nome de professor"></div>
        <div class="campo"><label>Senha</label><input type="password" name="senha" required placeholder="Crie ou digite sua senha"></div>
        <button class="btn-principal">Entrar →</button>
        <?php if(isset($erro_prof)) echo "<p class='erro'>$erro_prof</p>"; ?>
      </form>
    </div>
    <div id="form-aluno" class="hidden">
      <form method="POST" id="form-login-aluno">
        <input type="hidden" name="acao" value="login_aluno">
        <div class="campo"><label>Seu nome</label><input name="nome" required placeholder="Ex: Lucas"></div>
        <div class="campo">
          <label>Matrícula</label>
          <input name="matricula" required inputmode="numeric" pattern="<?= date('Y') ?>\d{5}" maxlength="9" placeholder="Ex: <?= date('Y') ?>23349" oninput="this.value = this.value.replace(/\D/g,'')">
          <p class="hint">9 dígitos, começando com <?= date('Y') ?>.</p>
        </div>
        <button class="btn-principal">Entrar →</button>
        <?php if(isset($erro_aluno)) echo "<p class='erro'>$erro_aluno</p>"; ?>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'prof'): ?>
<header>
  <div class="logo-mini"><h1>FOCO</h1><span class="badge">PROFESSOR</span></div>
  <div class="acoes">
    <span class="nome-user">Olá, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?></span>
    <a href="?sair=1" style="background:#FDECEA; padding:12px 20px; border-radius:10px; color:#9B3A30; text-decoration:none; font-weight:600;">Sair</a>
  </div>
</header>
<div class="container">
  <h2>Postar nova atividade</h2>
  <div class="form-box">
    <form method="POST" id="form-atividade">
      <input type="hidden" name="acao" value="criar_atividade">
      <label>Matéria</label>
      <select name="materia">
        <option>Matemática</option><option>Português</option><option>História</option>
        <option>Ciências</option><option>Geografia</option><option>Inglês</option>
      </select>
      <label>Título da atividade</label>
      <input name="titulo" required placeholder="Ex: Frações">
      <label>Conteúdo</label>
      <textarea name="conteudo" required placeholder="Cole aqui o texto que o aluno vai estudar."></textarea>
      <div id="perguntas-container"></div>
      <button type="button" class="btn-add" onclick="adicionarPergunta()">+ Adicionar pergunta ao quiz</button>
      <button type="submit" class="btn-principal" style="margin-top:24px;">Publicar atividade</button>
    </form>
  </div>
  
  <h2>Atividades publicadas</h2>
  <?php if(empty($atividades)): ?><p>Nenhuma atividade publicada ainda.</p><?php else: ?>
    <?php foreach ($atividades as $a): ?>
      <div class="ativ-prof">
        <div>
          <strong><?= htmlspecialchars($a['titulo']) ?></strong>
          <div class="meta">
            <span style="display:flex; align-items:center; gap:4px; color:var(--verde-escuro); font-weight:600;">
              <?= htmlspecialchars($a['materia']) ?>
            </span>
            <span style="display:flex; align-items:center; gap:4px; color:var(--vermelho);">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              <?= count($a['perguntas']) ?> perguntas
            </span>
          </div>
        </div>
        <a href="?excluir_ativ=<?= $a['id'] ?>" onclick="return confirm('Excluir esta atividade?')" class="btn-excluir">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          Excluir
        </a>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
  
  <h2 style="margin-top:48px;">Progresso dos alunos</h2>
  <?php if(empty($alunos)): ?><p>Nenhum aluno acessou o site ainda.</p><?php else: ?>
    <?php foreach ($alunos as $al): ?>
      <div class="aluno-card">
        <div class="aluno-avatar"><?= strtoupper(substr($al['nome'], 0, 1)) ?></div>
        <div class="aluno-info">
          <h5><?= htmlspecialchars($al['nome']) ?> <span style="font-weight:400; color:var(--cinza); font-size:0.85rem;">(<?= htmlspecialchars($al['matricula'] ?? 'sem matrícula') ?>)</span></h5>
          <div class="stats"><?= $al['feitas'] ?> atividades concluídas</div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'aluno'): ?>
<header>
  <div class="logo-mini"><h1>FOCO</h1><span class="badge">ALUNO</span></div>
  <div class="acoes">
    <button class="btn-leitura" id="btn-leitura" onclick="toggleLeitura()">Modo leitura</button>
    <span class="nome-user">Olá, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?></span>
    <a href="?sair=1" style="background:#FDECEA; padding:12px 20px; border-radius:10px; color:#9B3A30; text-decoration:none; font-weight:600;">Sair</a>
  </div>
</header>
<div class="container">
  <div id="view-pastas">
    <h2>Minhas matérias</h2>
    <?php $por_materia = []; foreach($atividades as $a) $por_materia[$a['materia']][] = $a; ?>
    <?php if(empty($por_materia)): ?><p>Nenhuma atividade disponível ainda. Aguarde seu professor!</p><?php else: ?>
      <div class="pastas">
        <?php foreach ($por_materia as $materia => $ativs): ?>
          <?php
            $qtd = count($ativs); $feitas = 0;
            foreach($ativs as $a) if(in_array($a['id'], $concluidas)) $feitas++;
            // CAMINHOS DOS ÍCONES DAS MATÉRIAS
            $icones = [
                'Matemática'=>'img/matematica.png', 
                'Português'=>'img/portugues.png', 
                'História'=>'img/historia.png', 
                'Ciências'=>'img/ciencias.png', 
                'Geografia'=>'img/geografia.png', 
                'Inglês'=>'img/ingles.png'
            ];
            $caminhoIcone = $icones[$materia] ?? 'img/default.png';
          ?>
          <div class="pasta" onclick="abrirMateria('<?= htmlspecialchars($materia, ENT_QUOTES) ?>')">
            <div class="icone"><img src="<?= $caminhoIcone ?>" alt="<?= $materia ?>"></div>
            <h4><?= htmlspecialchars($materia) ?></h4>
            <p><?= $feitas ?>/<?= $qtd ?> concluída<?= $qtd > 1 ? 's' : '' ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    
    <h2 style="margin-top:48px;">Meu progresso</h2>
    <?php $pct = $total_geral > 0 ? round((count($concluidas) / $total_geral) * 100) : 0; ?>
    <div class="progresso-box">
      <div class="progresso-header"><strong><?= $pct ?>% concluído</strong><span style="color:var(--cinza); font-size:0.95rem;"><?= count($concluidas) ?> de <?= $total_geral ?></span></div>
      <div class="barra"><div class="barra-fill" style="width:<?= $pct ?>%"></div></div>
    </div>
  </div>
  
  <div id="view-materia" class="hidden">
    <button class="btn-leitura" onclick="voltarPastas()" style="margin-bottom:24px; background:#FFF; border:2px solid var(--creme-escuro); color:var(--verde-escuro);">← Voltar para matérias</button>
    <h2 id="titulo-materia"></h2>
    <div id="lista-ativ-aluno"></div>
  </div>
</div>

<div id="modal-quiz">
  <div class="quiz-card">
    <div class="quiz-header">
      <h3 id="quiz-titulo" style="color:var(--verde-escuro);">Quiz</h3>
      <span class="quiz-progresso" id="quiz-contador">1 / 1</span>
    </div>
    <div id="quiz-corpo"></div>
  </div>
</div>

<form id="form-salvar" method="POST" style="display:none;">
    <input type="hidden" name="acao" value="salvar_progresso">
    <input type="hidden" name="atividade_id" id="f-ativ">
    <input type="hidden" name="acertos" id="f-acertos">
    <input type="hidden" name="total" id="f-total">
</form>

<script>
const atividadesPHP = <?= json_encode($atividades, JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const concluidasPHP = <?= json_encode($concluidas) ?>;

function abrirMateria(materia) {
    document.getElementById('view-pastas').classList.add('hidden');
    document.getElementById('view-materia').classList.remove('hidden');
    document.getElementById('titulo-materia').textContent = materia;
    const lista = document.getElementById('lista-ativ-aluno');
    const filtradas = atividadesPHP.filter(a => a.materia === materia);
    lista.innerHTML = filtradas.map(a => {
        const feita = concluidasPHP.includes(a.id);
        const perguntasJson = JSON.stringify(a.perguntas).replace(/'/g, "&#39;");
        return `
            <div class="ativ-aluno">
                <h4>${a.titulo} ${feita ? `<span class="badge-feito"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> CONCLUÍDO</span>` : ''}</h4>
                <p>${a.conteudo.replace(/\n/g, '<br>')}</p>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    ${a.perguntas.length ? `<button class="btn-quiz" onclick='abrirQuiz(${a.id}, ${perguntasJson})'>${feita ? 'Refazer quiz' : 'Fazer quiz'} (${a.perguntas.length} pergunta${a.perguntas.length > 1 ? 's' : ''})</button>` : ''}
                </div>
            </div>
        `;
    }).join('');
}

function voltarPastas() {
    document.getElementById('view-pastas').classList.remove('hidden');
    document.getElementById('view-materia').classList.add('hidden');
}

let quizAtual = {};
let qIndex = 0;
let acertos = 0;

function abrirQuiz(id, perguntas) {
    quizAtual = { id, perguntas };
    qIndex = 0; acertos = 0;
    document.getElementById('modal-quiz').classList.add('show');
    renderPergunta();
}

function renderPergunta() {
    const q = quizAtual.perguntas[qIndex];
    if(!q) { finalizar(); return; }
    document.getElementById('quiz-contador').textContent = `${qIndex + 1} / ${quizAtual.perguntas.length}`;
    const opcoes = q.opcoes_array;
    let html = `<p style="font-weight:bold; margin-bottom:20px; font-size:1.15rem;">${q.enunciado}</p>`;
    opcoes.forEach((op, i) => {
        html += `<label class="opcao"><input type="radio" name="resp" value="${i}"> ${op}</label>`;
    });
    html += `<button class="btn-principal" onclick="confirmar(${q.correta})">Confirmar resposta</button>`;
    html += `<div id="feedback-quiz" class="feedback"></div>`;
    const corpo = document.getElementById('quiz-corpo');
    corpo.innerHTML = html;
    corpo.scrollTop = 0;
}

function confirmar(correta) {
    const sel = document.querySelector('input[name="resp"]:checked');
    if(!sel) return;
    const escolhida = parseInt(sel.value);
    const acertou = escolhida === correta;
    const fb = document.getElementById('feedback-quiz');
    document.querySelectorAll('.opcao input').forEach(i => i.disabled = true);
    const btnConfirmar = document.querySelector('#quiz-corpo .btn-principal');
    if(btnConfirmar) btnConfirmar.style.display = 'none';
    let isUltima = (qIndex === quizAtual.perguntas.length - 1);
    let textoBotao = isUltima ? 'Ver resultado 🏆' : 'Próxima pergunta →';
    if(acertou) {
        acertos++;
        fb.className = 'feedback acerto';
        fb.innerHTML = '<span class="emoji">🎉</span> <br> Acertou! Mandou muito bem!';
        soltarConfetes();
    } else {
        const q = quizAtual.perguntas[qIndex];
        fb.className = 'feedback erro';
        fb.innerHTML = `<span class="emoji">💪</span> <br> Quase! A correta era: <strong>${q.opcoes_array[correta]}</strong>`;
    }
    const btnAvancar = document.createElement('button');
    btnAvancar.className = 'btn-avancar';
    btnAvancar.innerHTML = textoBotao;
    btnAvancar.onclick = () => { qIndex++; renderPergunta(); };
    document.getElementById('quiz-corpo').appendChild(btnAvancar);
}

function finalizar() {
    const pct = Math.round((acertos / quizAtual.perguntas.length) * 100);
    let emoji, titulo;
    if (pct === 100) { emoji='🏆'; titulo='PERFEITO!'; }
    else if (pct >= 70) { emoji='🎉'; titulo='Muito bem!'; }
    else if (pct >= 50) { emoji='💪'; titulo='Bom trabalho!'; }
    else { emoji='📚'; titulo='Continue tentando!'; }
    document.getElementById('quiz-corpo').innerHTML = `
        <div class="tela-final">
            <div class="grande-emoji">${emoji}</div>
            <h3>${titulo}</h3>
            <p>Você acertou</p>
            <div class="pontuacao">${acertos} / ${quizAtual.perguntas.length}</div>
            <p style="color:var(--cinza);">${pct}% de aproveitamento</p>
            <p style="margin-top:24px; color:var(--cinza); font-size:0.9rem;">Salvando progresso...</p>
        </div>`;
    if (pct >= 70) soltarConfetes(60);
    document.getElementById('f-ativ').value = quizAtual.id;
    document.getElementById('f-acertos').value = acertos;
    document.getElementById('f-total').value = quizAtual.perguntas.length;
    setTimeout(() => {
        document.getElementById('modal-quiz').classList.remove('show');
        document.getElementById('form-salvar').submit();
    }, 3500);
}

function soltarConfetes(qtd = 30) {
    const cores = ['#2BBAA5', '#93D3AE', '#FAECB6', '#F9A822', '#F96635'];
    for (let i = 0; i < qtd; i++) {
        const c = document.createElement('div');
        c.className = 'confete';
        c.style.left = Math.random() * 100 + 'vw';
        c.style.background = cores[Math.floor(Math.random() * cores.length)];
        c.style.animationDelay = Math.random() * 0.4 + 's';
        c.style.animationDuration = (1.5 + Math.random() * 1.5) + 's';
        c.style.width = (8 + Math.random() * 8) + 'px';
        c.style.height = (10 + Math.random() * 10) + 'px';
        document.body.appendChild(c);
        setTimeout(() => c.remove(), 4000);
    }
}

function toggleLeitura() {
    document.body.classList.toggle('leitura');
    document.getElementById('btn-leitura').classList.toggle('ativo');
}
</script>
<?php endif; ?>

<script>
function trocarTab(tipo) {
    const btnProf = document.getElementById('btn-tab-prof');
    const btnAluno = document.getElementById('btn-tab-aluno');
    if(tipo === 'prof') {
        btnProf.classList.add('ativo'); btnAluno.classList.remove('ativo');
        document.getElementById('form-prof').classList.remove('hidden');
        document.getElementById('form-aluno').classList.add('hidden');
    } else {
        btnAluno.classList.add('ativo'); btnProf.classList.remove('ativo');
        document.getElementById('form-aluno').classList.remove('hidden');
        document.getElementById('form-prof').classList.add('hidden');
    }
}

let contadorPerguntas = 0;
function adicionarPergunta() {
    contadorPerguntas++;
    const container = document.getElementById('perguntas-container');
    const bloco = document.createElement('div');
    bloco.className = 'pergunta-bloco';
    bloco.id = 'pergunta-' + contadorPerguntas;
    bloco.innerHTML = `
        <button type="button" class="remover" onclick="removerPergunta(${contadorPerguntas})">✕</button>
        <h5>❓ Pergunta ${contadorPerguntas}</h5>
        <input name="pergunta[]" placeholder="Digite a pergunta..." style="margin-bottom:10px;" required>
        <input name="opcoes[]" placeholder="Alternativas separadas por vírgula (ex: 3, 5, 7)" style="margin-bottom:10px;" required>
        <input name="correta[]" type="number" placeholder="Número da correta (ex: 2)" required>
    `;
    container.appendChild(bloco);
}
function removerPergunta(id) {
    const el = document.getElementById('pergunta-' + id);
    if (el) el.remove();
}
</script>
</body>
</html>