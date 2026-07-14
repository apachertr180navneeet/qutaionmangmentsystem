
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    function setFlesh(status, message = '') {
        Toast.fire({
            icon: status,
            title: message
        })
    }
</script>
@if(Session::has('success'))
<script>
    Toast.fire({
        icon: 'success',
        title: "{{ !empty(Session::get('message')) ? Session::get('message') :Session::get('success') }}"
    })
</script>
@endif
@if(Session::has('error'))
<script>
    Toast.fire({
        icon: 'error',
        title: "{{ !empty(Session::get('message')) ? Session::get('message') :Session::get('error') }}"
    })
</script>
@endif
@if(Session::has('warning'))
<script>
    Toast.fire({
        icon: 'warning',
        title: "{{ !empty(Session::get('message')) ? Session::get('message') :Session::get('warning') }}"
    })
</script>
@endif
@if($errors->any())
<script>
    var errorMessages = [];
    @foreach($errors->all() as $error)
        errorMessages.push("{{ $error }}");
    @endforeach

    if (errorMessages.length <= 3) {
        errorMessages.forEach(function(msg) {
            Toast.fire({
                icon: 'error',
                title: msg
            });
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Validation Errors',
            html: '<ul style="text-align:left; margin:0; padding-left:20px;">' + errorMessages.map(function(msg){ return '<li style="margin-bottom:5px;">' + msg + '</li>'; }).join('') + '</ul>',
            confirmButtonColor: '#4A00E0'
        });
    }
</script>
@endif