<table class="table-boleto" cellpadding="0" cellspacing="0" border="0">
    <tbody>
    <tr>
        <td valign="bottom" colspan="8" class="noborder nopadding">
            <div class="logocontainer" style="margin-bottom: 8px;">
                <div class="logobanco">
                    <img style="height: 32px; margin-top: 8px" src="{{ isset($logo_banco_base64) && !empty($logo_banco_base64) ? $logo_banco_base64 : 'https://dummyimage.com/150x75/fff/000000.jpg&text=+' }}" alt="logo do banco">
                </div>
                <div class="codbanco">{{ $codigo_banco_com_dv }}</div>
            </div>
            <div class="linha-digitavel">{{ $linha_digitavel }}</div>
        </td>
    </tr>
    <tr>
        <td colspan="7" class="top-2">
            <div class="titulo">Local de pagamento</div>
            <div class="conteudo">{{ $local_pagamento }}</div>
        </td>
        <td width="180" class="top-2">
            <div class="titulo">Vencimento</div>
            <div class="conteudo rtl">{{ $data_vencimento->format('d/m/Y') }}</div>
        </td>
    </tr>
    <tr class="@if($mostrar_endereco_ficha_compensacao) duas-linhas @endif">
        <td colspan="7">
            <div class="titulo">Beneficiário</div>
            <div class="conteudo">{{ $beneficiario['nome_documento'] }}</div>
            @if($mostrar_endereco_ficha_compensacao)<div class="conteudo">{{ $beneficiario['endereco_completo'] }}</div>@endif
        </td>
        <td>
            <div class="titulo">Agência/Código beneficiário</div>
            <div class="conteudo rtl">{{ $agencia_codigo_beneficiario }}</div>
        </td>
    </tr>
    <tr>
        <td width="110" colspan="2">
            <div class="titulo">Data do documento</div>
            <div class="conteudo">{{ $data_documento->format('d/m/Y') }}</div>
        </td>
        <td width="120" colspan="2">
            <div class="titulo">Nº doc.</div>
            <div class="conteudo">{{ $numero_documento }}</div>
        </td>
        <td width="60">
            <div class="titulo">Espécie doc.</div>
            <div class="conteudo">{{ $especie_doc }}</div>
        </td>
        <td>
            <div class="titulo">Aceite</div>
            <div class="conteudo">{{ $aceite }}</div>
        </td>
        <td width="110">
            <div class="titulo">Data processamento</div>
            <div class="conteudo">{{ $data_processamento->format('d/m/Y') }}</div>
        </td>
        <td>
            <div class="titulo">Nosso número</div>
            <div class="conteudo rtl">{{ $nosso_numero_boleto }}</div>
        </td>
    </tr>
    <tr>
        @if(!isset($esconde_uso_banco) || !$esconde_uso_banco)
            <td colspan="2">
                <div class="titulo">Uso do banco</div>
                <div class="conteudo">{{ $uso_banco }}</div>
            </td>
        @endif

        <td {{isset($esconde_uso_banco) && $esconde_uso_banco ? 'colspan=3': '' }}>
            <div class="titulo">Carteira</div>
            <div class="conteudo">{{ $carteira_nome }}</div>
        </td>
        <td width="35">
            <div class="titulo">Espécie</div>
            <div class="conteudo">{{ $especie }}</div>
        </td>
        <td colspan="2">
            <div class="titulo">Quantidade</div>
            <div class="conteudo"></div>
        </td>
        <td width="110">
            <div class="titulo">Valor</div>
            <div class="conteudo"></div>
        </td>
        <td>
            <div class="titulo">(=) Valor do Documento</div>
            <div class="conteudo rtl">{{ $valor }}</div>
        </td>
    </tr>
    <tr>
        <td colspan="7">
            <div class="titulo">Instruções de responsabilidade do beneficiário. Qualquer dúvida sobre este boleto, contate o beneficiário</div>
        </td>
        <td>
            <div class="titulo">(-) Descontos / Abatimentos</div>
            <div class="conteudo rtl"></div>
        </td>
    </tr>
    <tr>
        <td colspan="7" rowspan="4" class="notopborder">
            @if (!empty($instrucoes[0]))
                <div class="conteudo">{{ $instrucoes[0] }}</div>
            @endif
            @if (!empty($instrucoes[1]))
                <div class="conteudo">{{ $instrucoes[1] }}</div>
            @endif
            @if (!empty($instrucoes[2]))
                <div class="conteudo">{{ $instrucoes[2] }}</div>
            @endif
            @if (!empty($instrucoes[3]))
                <div class="conteudo">{{ $instrucoes[3] }}</div>
            @endif
            @if (!empty($instrucoes[4]))
                <div class="conteudo">{{ $instrucoes[4] }}</div>
            @endif
            @if (!empty($instrucoes[5]))
                <div class="conteudo">{{ $instrucoes[5] }}</div>
            @endif
            @if (!empty($instrucoes[6]))
                <div class="conteudo">{{ $instrucoes[6] }}</div>
            @endif
            @if (!empty($instrucoes[7]))
                <div class="conteudo">{{ $instrucoes[7] }}</div>
            @endif
        </td>
        <td>
            <div class="titulo">(-) Outras deduções</div>
            <div class="conteudo rtl"></div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="titulo">(+) Mora / Multa {{ $codigo_banco == '104' ? '/ Juros' : '' }}</div>
            <div class="conteudo rtl"></div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="titulo">(+) Outros acréscimos</div>
            <div class="conteudo rtl"></div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="titulo">(=) Valor cobrado</div>
            <div class="conteudo rtl"></div>
        </td>
    </tr>
    <tr>
        <td colspan="7">
            <div style="display: block; width: 100%; min-height: 60px">
                <div class="titulo">Pagador</div><div class="conteudo" style="position: absolute; right: 196px">{{ strlen($pagador['documento']) == 14 ? 'CPF: ' : 'CNPJ: ' }}{{ $pagador['documento'] }}</div>
                <div class="conteudo">{{ $pagador['nome'] }}</div>
                <div class="conteudo">{{ $pagador['endereco'] }}, {{ $pagador['bairro'] }}</div>
                <div class="conteudo">{{ $pagador['endereco2'] }}</div>
            </div>
        </td>
        <td class="noleftborder">
            <div class="titulo" style="vertical-align: bottom; margin-top:44px">Cód. Baixa</div>
        </td>
    </tr>

    <tr>
        <td colspan="6" class="noleftborder">
            <div class="titulo">Sacador/Avalista
                <div class="conteudo sacador">{{ $sacador_avalista ? $sacador_avalista['nome_documento'] : '' }}</div>
            </div>
        </td>
        <td colspan="2" class="norightborder noleftborder">
            <div class="conteudo noborder rtl">Autenticação mecânica - Ficha de Compensação</div>
        </td>
    </tr>

    <tr>
        <td colspan="8" class="noborder" {!! $i > 0 && ($i+1) % 3 == 0 ? '' : 'style="padding-bottom: 8px"' !!}>
            {!! $codigo_barras !!}
        </td>
    </tr>

    </tbody>
</table>
