{{-- Riset disertasi: menandai waktu formulir dibuka sebagai awal pengisian.
     Dipasangkan dengan RisetLogger::catatEntri() di controller store(). --}}
<input type="hidden" name="_mulai_input" value="{{ now()->toIso8601String() }}">
