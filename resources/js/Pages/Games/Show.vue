<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed, ref } from 'vue'

const boardState = ref([0, 0, 0, 0, 0, 0, 0, 0, 0])

const xTurn = computed(
    () => boardState.value.reduce((carry, value) => carry + value, 0) === 0
)

const fillSquare = (index) => {
    boardState.value[index] = xTurn.value ? -1 : 1
}
</script>

<template>
    <AuthenticatedLayout>
        <menu
            class="mx-auto mt-12 grid w-0 min-w-fit grid-cols-3 gap-1.5 dark:text-gray-100"
        >
            <li
                v-for="(square, index) in boardState"
                class="grid size-28 place-items-center bg-gray-300 sm:size-32 dark:bg-gray-700"
            >
                <button
                    @click="fillSquare(index)"
                    v-if="square === 0"
                    class="place-self-stretch bg-gray-200 transition-colors hover:bg-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700"
                ></button>
                <span
                    v-else
                    v-text="square === -1 ? 'X' : 'O'"
                    class="text-4xl font-bold"
                ></span>
            </li>
        </menu>
    </AuthenticatedLayout>
</template>
