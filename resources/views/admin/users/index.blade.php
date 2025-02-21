<h1>Users</h1>
@forelse ($users as $user)
    <table>
        <thead>
            <th>Nome</th>
            <th>Email</th>
            <th>ID</th>
        </thead>
        <tbody>
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->id }}</td>
            </tr>
        </tbody>
    </table>
@empty
    <h3>Não há nenhum usuário!</h3>
@endforelse