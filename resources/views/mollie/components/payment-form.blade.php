<div>
    @if (!config('lunar.mollie.specify_payment_methods'))
        <div class="group cursor-pointer mb-2 border p-3 flex items-center justify-between rounded-lg hover:bg-blue-100 hover:border-blue-500"
             wire:click="handleSubmit">
            <div class="flex items-center space-x-4">
                <span>{{ trans('lunar::mollie.secure_payment_help_text') }}</span>
            </div>
            <button class="flex items-center px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-500 disabled:opacity-50"
                    type="submit">
                <span>{{ trans('lunar::mollie.pay_button') }}</span>
            </button>
        </div>
    @else
        @foreach ($this->getPaymentMethods() as $paymentMethod)
            <div class="group cursor-pointer mb-2 border p-3 flex items-center justify-between rounded-lg hover:bg-blue-100 hover:border-blue-500"
                 wire:click="handleSubmit('{{ $paymentMethod }}')">
                <div class="flex items-center space-x-4">
                    <img src="{{ $paymentMethod->getImageSrc('svg') }}" width="70" alt="{{ $paymentMethod->value }}"/>
                    <span>{{ $paymentMethod->getName() }}</span>
                </div>
                <button class="flex items-center px-5 py-3 text-sm font-medium text-white bg-white hover:!bg-blue-500 focus:bg-blue-500 group-hover:bg-blue-600 rounded-lg"
                        type="submit">
                    <span>{{ trans('lunar::mollie.pay_with_method_button', ['method' => $paymentMethod->getName()]) }}</span>
                </button>
            </div>
        @endforeach
    @endif
</div>
