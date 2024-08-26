<?php
require_once ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");
$site = new Woreda();
?>
<div class="w-full h-full mx-auto p-0 text-center">
    <div class="hidden md:flex justify-center items-center float-left w-1/2 min-h-screen text-white text-[28pt] bg-[#f33926]">
        woreda
    </div>
    <div class="flex justify-center items-center w-full md:w-1/2 float-left min-h-screen bg-[#f6ece2]">
        <div class="w-1/2 min-w-[380px] px-[50px] py-[20px] rounded-[10px] border border-[#efefef] bg-white shadow-[6px_7px_5px_-8px_rgba(0,0,0,0.5)]">
            <form action="/process/login" method="POST">
                <input type="text" class="w-[250px] text-[18px] mt-[5px] mb-[10px] p-[10px] rounded-[5px] border border-[#dcdcdc]" placeholder="Email" name="user" size="18"><br>
                <input type="password" class="w-[250px] text-[18px] mt-[5px] mb-[10px] p-[10px] rounded-[5px] border border-[#dcdcdc]" placeholder="Password" name="pass" size="18"><br>
                <input type="hidden" name="r" value="https://woreda.samifouad.com">
                <button type="submit" class="w-4/5 text-[22px] mt-[10px] mb-0 p-[10px] rounded-[5px] border border-white bg-[#f33926] text-white" size="22" value="Log In">Log In</button>
                <br><br>
                <hr class="border border-[#efefef]">
                <br>
                <span class="text-sm">speak to your team leader to request access or for help with login issues</span>
                <br>
            </form>
        </div>
    </div>
</div>