<div class="flex space-x-1 justify-around">
    @if($status=="PROCESSED")
    <x-modal :value="$id">
        <x-slot name="trigger">
            <button class="p-1 text-blue-600 dark:text-slate-300 border bg-gray-300 dark:bg-slate-800 hover:bg-blue-600 hover:text-white rounded text-xs">
                Syn
            </button>
        </x-slot>
        <table class="text-sm mb-2 text-black">
            <tr>
                <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium">StatusCode</td>
                <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium w-full">{{$code}}</td>
            </tr>
            <tr>
                <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium">MessageID</td>
                <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium w-full">{{$mid}}</td>
            </tr>
            <tr>
                <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium">To</td>
                <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium w-full" x-data="{alert:false}">
                    <input class="bg-white bordered dark:bg-slate-300" value="{{$msisdn}}" />
                </td>
            </tr>
            <tr>
                <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium">Status</td>
                <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium w-full">{{$status}}</td>
            </tr>
        </table>

        <input id="phone" class="hidden" value="{{$msisdn}}" />

        <p class="text-sm mb-2 text-black">Update to Status</p>
        <a href="{{ route('admin.update.sms.status', [$id,'DELIVERED']) }}" class="p-1 m-1 text-xs bg-green-100 text-green-600 hover:bg-green-500 hover:text-white rounded">
            DELIVERED
        </a>
        <a href="{{ route('admin.update.sms.status', [$id,'ACCEPTED']) }}" class="p-1 m-1 text-xs bg-blue-100 text-blue-600 hover:bg-blue-500 hover:text-white rounded">
            ACCEPTED
        </a>
        <a href="{{ route('admin.update.sms.status', [$id,'UNDELIVERED']) }}" class="p-1 m-1 text-xs bg-red-100 text-red-600 hover:bg-red-500 hover:text-white rounded">
            UNDELIVERED
        </a>
    </x-modal>
    <script>
        // Prepare action buttons
        const buttonContainers = document.querySelectorAll('.copy');

        for (const buttonContainer of buttonContainers) {
            const buttons = buttonContainer.querySelectorAll('button');
            const pasteTarget = buttonContainer.getAttribute('data-for');

            for (const button of buttons) {
                const elementName = button.getAttribute('data-el');
                button.addEventListener('click', () => insertText(pasteTarget))
            }
        }

        // Inserts text at cursor, or replaces selected text
        function insertText(selector) {
            const text = document.querySelector(selector);
            try {
                // Security exception may be thrown by some browsers
                const textArea = document.createElement('textarea');
                textArea.value = text.value;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('Copy');
                textArea.remove();
            } catch (ex) {
                console.warn("Copy to clipboard failed.", ex);
                return false;
            } finally {
            }
        }
    </script>
    @endif
</div>
