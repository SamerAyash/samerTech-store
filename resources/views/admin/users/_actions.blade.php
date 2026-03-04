<div class="d-flex justify-content-end flex-wrap">
    <a href="{{ route('admin.users.show', $user) }}" 
       class="btn btn-icon btn-light btn-hover-primary btn-sm mr-1" 
       data-toggle="tooltip" 
       title="View Profile">
        <i class="flaticon-eye"></i>
    </a>
    <button type="button" 
            class="btn btn-icon btn-light btn-hover-warning btn-sm mr-1" 
            data-toggle="tooltip" 
            title="Change Status"
            onclick="updateUserStatus({{ $user->id }}, '{{ $user->status ?? 'active' }}')">
        <i class="flaticon2-protection"></i>
    </button>
    <button type="button" 
            class="btn btn-icon btn-light btn-hover-danger btn-sm" 
            data-toggle="tooltip" 
            title="Delete"
            onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->orders_count ?? 0 }})">
        <i class="flaticon2-trash"></i>
    </button>
</div>

