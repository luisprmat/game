<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed, onUnmounted, ref } from 'vue'
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useGameState, gameStates } from '@/Composables/useGameState'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps(['game'])

const boardState = ref(props.game.state ?? [0, 0, 0, 0, 0, 0, 0, 0, 0])
const gameState = useGameState()
const players = ref([])

const page = usePage()
const xTurn = computed(
    () => boardState.value.reduce((carry, value) => carry + value, 0) === 0
)
const youTurn = computed(() => {
    if (props.game.player_one_id === page.props.auth.user.id) {
        return xTurn.value
    }

    return !xTurn.value
})

const lines = [
    // rows
    [0, 1, 2],
    [3, 4, 5],
    [6, 7, 8],

    // columns
    [0, 3, 6],
    [1, 4, 7],
    [2, 5, 8],

    // diagonals
    [0, 4, 8],
    [2, 4, 6],
]

const fillSquare = (index) => {
    if (!youTurn.value) {
        return
    }

    boardState.value[index] = xTurn.value ? -1 : 1

    router.put(route('games.update', props.game.id), {
        state: boardState.value,
    })

    checkForVictory()
}

const checkForVictory = () => {
    const winningLine = lines
        .map((line) =>
            line.reduce((carry, index) => carry + boardState.value[index], 0)
        )
        .find((sum) => Math.abs(sum) === 3)

    if (winningLine === -3) {
        gameState.change(gameStates.XWins)
        return
    }

    if (winningLine === 3) {
        gameState.change(gameStates.OWins)
        return
    }

    if (!boardState.value.includes(0)) {
        gameState.change(gameStates.Stalemate)
        return
    }

    gameState.change(gameStates.InProgress)
}

const resetGame = () => {
    boardState.value = [0, 0, 0, 0, 0, 0, 0, 0, 0]
    gameState.change(gameStates.InProgress)

    router.put(route('games.update', props.game.id), {
        state: boardState.value,
    })
}

Echo.join(`games.${props.game.id}`)
    .here((users) => (players.value = users))
    .joining((user) =>
        router.reload({
            onSuccess: () => players.value.push(user),
        })
    )
    .leaving(
        (user) =>
            (players.value = players.value.filter(({ id }) => id !== user.id))
    )
    .listen('PlayerMadeMove', ({ game }) => {
        boardState.value = game.state
        checkForVictory()
    })

onUnmounted(() => {
    Echo.leave(`games.${props.game.id}`)
})
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

        <ul
            class="mx-auto mt-6 max-w-sm space-y-2 ps-4 sm:ps-0 dark:text-gray-200"
        >
            <li class="flex items-center gap-2">
                <span
                    class="rounded bg-gray-200 p-1.5 font-bold dark:bg-gray-700"
                    :class="{ 'bg-green-300 dark:bg-green-700': xTurn }"
                    >X</span
                >
                <span>{{ game.player_one.name }}</span>
                <span
                    :class="{
                        '!bg-green-500': players.find(
                            ({ id }) => id === game.player_one_id
                        ),
                    }"
                    class="size-2 rounded-full bg-red-500"
                ></span>
            </li>

            <li v-if="game.player_two" class="flex items-center gap-2">
                <span
                    class="rounded bg-gray-200 p-1.5 font-bold dark:bg-gray-700"
                    :class="{ 'bg-green-300 dark:bg-green-700': !xTurn }"
                    >0</span
                >
                <span>{{ game.player_two.name }}</span>
                <span
                    :class="{
                        '!bg-green-500': players.find(
                            ({ id }) => id === game.player_two_id
                        ),
                    }"
                    class="size-2 rounded-full bg-red-500"
                ></span>
            </li>
            <li v-else>{{ $t('Waiting for player two') }}...</li>
        </ul>

        <Modal @close="resetGame()" :show="gameState.hasEnded()">
            <div class="p-6">
                <div
                    class="my-12 text-center font-mono text-6xl font-bold uppercase"
                >
                    <span
                        v-if="gameState.current() === gameStates.XWins"
                        class="text-green-600"
                        >{{ $t(':player has won!', { player: 'X' }) }}</span
                    >
                    <span
                        v-else-if="gameState.current() === gameStates.OWins"
                        class="text-green-600"
                        >{{ $t(':player has won!', { player: 'O' }) }}</span
                    >
                    <span v-else class="text-orange-600">{{
                        $t('Stalemate!')
                    }}</span>
                </div>

                <div class="mt-6 flex justify-end">
                    <PrimaryButton @click="resetGame()">{{
                        $t('Play Again')
                    }}</PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
