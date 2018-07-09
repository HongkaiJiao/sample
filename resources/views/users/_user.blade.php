<li>
    <img src="{{ $user->gravatar() }}" alt="{{ $user->name }}" class="gravatar" />
    <a class="username" href="{{ route('users.show',$user->id) }}">{{ $user->name }}</a>

    @can('destroy',$user)
        <form method="post" action="{{ route('users.destroy',$user->id) }}">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button class="btn btn-danger btn-sm delete-btn" type="submit">删除</button>
        </form>
    @endcan
</li>