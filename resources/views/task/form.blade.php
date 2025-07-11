@extends('layout')

@section('content')
    <h1>Создать задачу</h1>
    <form method="POST" action="{{ url('/task/create') }}">
        @csrf
        <input type="text" name="title" placeholder="Тема задачи" required />
        <textarea name="description" rows="5" placeholder="Описание задачи" required></textarea>
        <button type="submit">Создать задачу</button>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('response.result.task.id'))
            Toastify({
                text: "✅ Задача создана, ID = {{ session('response.result.task.id') }}",
                duration: 5000,
                gravity: "top",
                position: "right",
                style: { background: "#34a853" },
            }).showToast();
            @elseif(session('response.error') || session('error'))
            Toastify({
                text: "❌ {{ session('response.error') ?? session('error') }}",
                duration: 5000,
                gravity: "top",
                position: "right",
                style: { background: "#d93025" },
            }).showToast();
            @endif
        });
    </script>
@endpush
