<div x-data="{
  processing: false,
  error: null,
  }">
  <!-- Display a payment form -->
  <form x-ref="payment-form" wire:submit.prevent="handleSubmit">
    <div class="mt-4">
      <button
        class="flex items-center px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-500 disabled:opacity-50"
        type="submit"
        x-bind:disabled="processing"
      >
        <span
          x-show="!processing"
        >
          Make Mollie Payment
        </span>
        <span
          x-show="processing"
          class="block mr-2"
        >
          <svg
            class="w-5 h-5 text-white animate-spin"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
        </span>
        <span
          x-show="processing"
        >
          Processing
        </span>
      </button>
    </div>
    <div x-show="error" x-text="error" class="p-3 mt-4 text-sm text-red-600 rounded bg-red-50"></div>
  </form>
</div>
