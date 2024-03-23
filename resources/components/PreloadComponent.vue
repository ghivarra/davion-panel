<template>
    <section class="loader-background">
        <div class="loader"></div>
    </section>
</template>

<script>

export default {
    name: 'preload-component',
    data: function() {
        return {
            
        }
    }
}

</script>

<style lang="scss">
@keyframes loader {
    100% {
        transform: rotate(.5turn)
    }
}

.loader {
    width: 75px;
    aspect-ratio: 1;
    display: grid;

    &::before,
    &::after {
        content: "";
        grid-area: 1/1;
        --c: no-repeat radial-gradient(farthest-side, var(--bs-primary) 92%, #0000);
        background:
            var(--c) 50% 0,
            var(--c) 50% 100%,
            var(--c) 100% 50%,
            var(--c) 0 50%;
        background-size: 15px 15px;
        animation: loader 1s infinite;
    }

    &::before {
        margin: 4px;
        filter: hue-rotate(135deg);
        background-size: 12px 12px;
        animation-timing-function: linear
    }

    &-background {
        min-height: 100vh;
        min-height: 100dvh;
        min-width: 100vw;
        min-width: 100dvw;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        background: white;
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0.8;
    }
}

.loader-enter-active,
.loader-leave-active {
    opacity: 0.8;
    transition: opacity 250ms linear;
}

.loader-enter-from,
.loader-leave-to {
    opacity: 0;
}

.loader-enter-to,
.loader-leave-from {
    opacity: 0.8;
}
</style>