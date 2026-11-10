@if(session('success'))
    <div class="toast toast-success" id="toast-message">
        <span class="toast-icon">✓</span>
        <span class="toast-message">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="toast toast-error" id="toast-message">
        <span class="toast-icon">⚠</span>
        <span class="toast-message">{{ session('error') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="toast toast-error" id="toast-message">
        <span class="toast-icon">⚠</span>
        <div class="toast-message">
            <ul class="toast-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toasts = document.querySelectorAll('.toast');
        
        if (toasts.length > 0) {
            setTimeout(function() {
                toasts.forEach(function(toast) {
                    toast.style.transition = 'opacity 0.5s ease';
                    toast.style.opacity = '0';
                    
                    setTimeout(function() {
                        toast.remove();
                    }, 500); 
                });
            }, 5000); 
        }
    });
</script>