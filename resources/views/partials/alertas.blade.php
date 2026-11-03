{{-- Mensagens exibidas no topo das páginas. --}}
@if (session('sucesso'))
    <div class="alerta alerta-sucesso" role="status">{{ session('sucesso') }}</div>
@endif

@if (session('erro'))
    <div class="alerta alerta-erro" role="alert">{{ session('erro') }}</div>
@endif

@if ($errors->any())
    <div class="alerta alerta-erro" role="alert">
        Corrija os campos abaixo:
        <ul>
            @foreach ($errors->all() as $mensagem)
                <li>{{ $mensagem }}</li>
            @endforeach
        </ul>
    </div>
@endif
