<?php
// Arquivo: orcamento.php
// Caminho do include corrigido para ser mais portável
include '../components/header.php'; 
?>

<style>
    .page-header-service {
        padding: 120px 0 60px 0;
        background: linear-gradient(45deg, var(--azul-escuro), var(--azul-principal));
        text-align: center;
        color: var(--branco);
    }
    .page-header-service h1 {
        font-size: 2.8rem;
        font-weight: 700;
    }
    .form-wrapper {
        padding: 80px 0;
        background-color: var(--cinza);
    }
    .quote-form {
        max-width: 800px;
        margin: 0 auto;
        background: var(--branco);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px var(--sombra);
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--azul-escuro);
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 15px;
        border: 2px solid #E0E0E0;
        border-radius: 8px;
        font-size: 1rem;
        font-family: 'Poppins', sans-serif;
    }
    /* Estilos para a área de instrumentos */
    #instrumentos-container {
        display: none; 
        background-color: #f8f9fa;
        border: 2px solid #E0E0E0;
        border-radius: 8px;
        padding: 20px;
        margin-top: 10px;
        max-height: 300px;
        overflow-y: auto;
    }
    .instrument-list {
        column-count: 2;
        column-gap: 20px;
    }
    .instrument-item {
        /* 1. Ativa o alinhamento flex e centraliza verticalmente */
        display: flex;
        align-items: center; 
        margin-bottom: 12px;
        /* Adicionado para evitar quebras estranhas de layout */
        break-inside: avoid;
        page-break-inside: avoid;
    }
    .instrument-item input[type="checkbox"] {
        /* 2. Garante que o checkbox não encolha e reseta margens */
        flex-shrink: 0;
        margin: 0 10px 0 0;
        /* Força um tamanho padrão para o checkbox */
        width: 16px;
        height: 16px;
    }
    .instrument-item label {
        /* 3. Garante que o texto se comporte corretamente */
        margin: 0;
        line-height: 1.4;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .instrument-list {
            column-count: 1;
        }
    }
</style>

<main>
    <section class="page-header-service">
        <div class="container">
            <h1>Solicite Seu Orçamento</h1>
        </div>
    </section>

    <section class="form-wrapper">
        <form id="quoteForm" class="quote-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nome">Seu Nome *</label>
                    <input type="text" id="nome" required>
                </div>
                <div class="form-group">
                    <label for="empresa">Empresa</label>
                    <input type="text" id="empresa">
                </div>
                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone / WhatsApp *</label>
                    <input type="tel" id="telefone" required>
                </div>

                <div class="form-group full-width">
                    <label for="tipoServico">Tipo de Serviço *</label>
                    <select id="tipoServico" required>
                        <option value="">-- Selecione um serviço --</option>
                        <option value="Calibração">Calibração</option>
                        <option value="Assistência Técnica">Assistência Técnica</option>
                        <option value="Qualificação">Qualificação</option>
                        <option value="Outros">Outros Serviços</option>
                    </select>
                </div>

                <div class="form-group full-width" id="instrumentos-container">
                    <label>Selecione o(s) Instrumento(s)</label>
                    <div id="instrumentos-list" class="instrument-list">
                        </div>
                </div>

                <div class="form-group full-width">
                    <label for="mensagem">Mensagem Adicional</label>
                    <textarea id="mensagem" rows="5"></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-solid-blue btn-form">Enviar Orçamento</button>
        </form>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- DADOS DOS SERVIÇOS E INSTRUMENTOS ---
    // ** LISTA COMPLETA E ATUALIZADA DE SERVIÇOS E INSTRUMENTOS **
    const servicosEInstrumentos = {
        'Calibração': [
            'Autoclave',
            'Balança Analítica',
            'Calibração de Instrumentos (Geral)',
            'Câmara Climática',
            'Centrífuga',
            'Condutivímetro',
            'Densímetro',
            'Densímetro Digital',
            'Equipamentos de Laboratório (Geral)',
            'Equipamentos de Medição (Geral)',
            'Espectrofotômetro',
            'Espectrofotômetro UV-VIS',
            'Estufa',
            'Manômetro',
            'Medidor de pH',
            'Micrômetro',
            'Equipamentos Micronal',
            'Paquímetro',
            'pHmetro',
            'Relógio Comparador',
            'Termômetro',
            'Turbidímetro'
        ],
        'Assistência Técnica': [
            'Autoclave (Manutenção e Reparo)',
            'Estufa (Manutenção e Reparo)',
            'Espectrofotômetro (Manutenção e Reparo)',
            'pHmetro (Manutenção e Reparo)',
            'Fotômetro de Chama (Manutenção e Reparo)',
            'Equipamentos Micronal (Manutenção e Reparo)',
            'Equipamentos de Laboratório (Geral)',
            'Manutenção Preventiva (Plano)'
        ],
        'Qualificação': [
            'Qualificação de Autoclave',
            'Qualificação de Autoclaves e Estufas',
            'Qualificação de Equipamentos Binder',
            'Qualificação de Câmara Climática',
            'Qualificação de Capela de Fluxo Laminar',
            'Qualificação de Espectrofotômetro',
            'Qualificação de Estufas',
            'Qualificação Térmica (Geral)',
            'Qualificação Térmica de Autoclave'
        ],
        'Outros': [
            'Treinamentos técnicos',
            'Consultoria em metrologia',
            'Aferição de Equipamentos',
            'Aferição de Instrumentos',
        ]
    };

    const servicoSelect = document.getElementById('tipoServico');
    const instrumentosContainer = document.getElementById('instrumentos-container');
    const instrumentosListDiv = document.getElementById('instrumentos-list');

    // Função que mostra/esconde a lista de instrumentos
    servicoSelect.addEventListener('change', function() {
        const servicoSelecionado = this.value;
        const instrumentos = servicosEInstrumentos[servicoSelecionado];

        // Limpa a lista anterior
        instrumentosListDiv.innerHTML = '';

        if (instrumentos && instrumentos.length > 0) {
            instrumentos.forEach(instrumento => {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('instrument-item');

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.id = instrumento.replace(/\s+/g, '-'); // Cria um ID tipo "Calibracao-de-Autoclave"
                checkbox.name = 'instrumentos';
                checkbox.value = instrumento;

                const label = document.createElement('label');
                label.htmlFor = checkbox.id;
                label.textContent = instrumento;
                
                itemDiv.appendChild(checkbox);
                itemDiv.appendChild(label);
                instrumentosListDiv.appendChild(itemDiv);
            });
            instrumentosContainer.style.display = 'block'; // Mostra o container
        } else {
            instrumentosContainer.style.display = 'none'; // Esconde o container
        }
    });

    // --- LÓGICA DE ENVIO PARA O WHATSAPP ---
    const quoteForm = document.getElementById('quoteForm');

    quoteForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Previne o envio padrão do formulário

        // ** IMPORTANTE: Substitua pelo seu número de WhatsApp **
        const numeroWhatsApp = '5561991084464'; // Use o formato: código do país + DDD + número

        // Coleta dos dados do formulário
        const nome = document.getElementById('nome').value;
        const empresa = document.getElementById('empresa').value || 'Não informado';
        const email = document.getElementById('email').value;
        const telefone = document.getElementById('telefone').value;
        const tipoServico = document.getElementById('tipoServico').value;
        const mensagemAdicional = document.getElementById('mensagem').value || 'Nenhuma';

        // Coleta dos instrumentos selecionados (checkboxes)
        const instrumentosSelecionados = [];
        const checkboxes = document.querySelectorAll('input[name="instrumentos"]:checked');
        checkboxes.forEach(checkbox => {
            instrumentosSelecionados.push(checkbox.value);
        });
        const instrumentosTexto = instrumentosSelecionados.length > 0 ? instrumentosSelecionados.join(', ') : 'Nenhum selecionado';

        // Formatação da mensagem para o WhatsApp (%0A é o código para quebra de linha)
        let mensagemFormatada = `*Nova Solicitação de Orçamento*%0A%0A`;
        mensagemFormatada += `*Nome:* ${nome}%0A`;
        mensagemFormatada += `*Empresa:* ${empresa}%0A`;
        mensagemFormatada += `*E-mail:* ${email}%0A`;
        mensagemFormatada += `*Telefone:* ${telefone}%0A%0A`;
        mensagemFormatada += `*Tipo de Serviço:* ${tipoServico}%0A`;
        if (instrumentosSelecionados.length > 0) {
            mensagemFormatada += `*Instrumentos:* ${instrumentosTexto}%0A`;
        }
        mensagemFormatada += `*Mensagem Adicional:* ${mensagemAdicional}%0A`;

        // Cria e abre o link do WhatsApp
        const urlWhatsApp = `https://wa.me/${numeroWhatsApp}?text=${mensagemFormatada}`;
        window.open(urlWhatsApp, '_blank');
    });
});
</script>

<?php 
// Caminho do include corrigido para ser mais portável
include '../components/footer.php'; 
?>