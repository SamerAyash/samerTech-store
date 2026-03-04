<div class="d-flex justify-content-end flex-wrap">
    <a href="{{ route('admin.contact.show', $contact) }}" 
       class="btn btn-icon btn-light btn-hover-primary btn-sm mr-1" 
       data-toggle="tooltip" 
       title="View Message">
        <i class="flaticon-eye"></i>
    </a>
    <button type="button" 
            class="btn btn-icon btn-light btn-hover-{{ $contact->readed ? 'warning' : 'success' }} btn-sm mr-1" 
            data-toggle="tooltip" 
            title="{{ $contact->readed ? 'Mark as Unread' : 'Mark as Read' }}"
            onclick="toggleReadStatus({{ $contact->id }}, {{ $contact->readed ? 'true' : 'false' }})">
        <i class="flaticon2-{{ $contact->readed ? 'mail' : 'check-mark' }}"></i>
    </button>
    <button type="button" 
            class="btn btn-icon btn-light btn-hover-danger btn-sm" 
            data-toggle="tooltip" 
            title="Delete"
            onclick="deleteContact({{ $contact->id }}, '{{ addslashes($contact->name) }}')">
        <i class="flaticon2-trash"></i>
    </button>
</div>

