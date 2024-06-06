<div class="dead-container">
    <span class="dead-txt">You died</span>
    <a class="respawn-btn" href="router.php?action=resetAll">Restart</a>
</div>

<style>
    .dead-container {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background-color: rgba(145, 17, 17, 0.7);
        z-index: 5;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        gap: 10%;
    }

    .dead-txt {
        font-size: 3rem;
    }

    .respawn-btn {
        border: solid 3px #000;
        border-radius: 10px;
        padding: 5px 20px;
        font-size: 2rem;
        background-color: #6f6f6f;
    }

    .choices-btn {
        display: none;
    }
</style>