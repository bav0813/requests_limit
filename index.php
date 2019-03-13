<?php
/**
     * Created by PhpStorm.
     * User: andrey
     * Date: 10.02.18
     * Time: 21:50
     */
?>


    <form id="submit_form" method="post" action="block_with_limit.php">

        Enter name <input type="text" name="name"> <br>
        Choose option:<br><p></p>
        <input type="radio" name="case1"value="serialize" checked>Serialize<br>
        <input type="radio" name="case1"value="json">JSON<br>

        <input type="submit">

    </form>
<hr>

<form id="submit_form2" method="post" action="block_with_limit_wait.php">

    Enter name <input type="text" name="name"> <br>
    Choose option:<br><p></p>
    <input type="radio" name="case2"value="serialize" checked>Serialize<br>
    <input type="radio" name="case2"value="json">JSON<br>

    <input type="submit">

</form>
