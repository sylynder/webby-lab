<!-- <h1 class="text-2xl font-bold my-4"></h1>
<ul>
    <?php foreach ($users as $user): ?>
        <li>
            <a href="/hx/user/<?=$user['id']?>"><?=$user['name']?></a>
        </li>
    <?php endforeach; ?>
</ul> -->


<h1 class="text-2xl font-bold my-4"></h1>
<ul>

    @foreach($users as $user)
        <li>
            <a href="/hx/user/<?=$user['id']?>"><?=$user['name']?></a>
        </li>
    @endforeach
</ul>

