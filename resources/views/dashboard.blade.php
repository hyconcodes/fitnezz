@php
    $user = auth()->user();
    if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
        $redirectRoute = 'admin.dashboard';
    } elseif ($user->hasRole('trainer')) {
        $redirectRoute = 'trainer.dashboard';
    } else {
        $redirectRoute = 'student.dashboard';
    }
@endphp

<script>
    window.location.href = "{{ route($redirectRoute) }}";
</script>

<div class="flex items-center justify-center min-h-screen">
    <div class="text-center">
        <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-500 mx-auto"></div>
        <p class="mt-4 text-gray-600 dark:text-gray-400">Redirecting to your dashboard...</p>
    </div>
</div>
