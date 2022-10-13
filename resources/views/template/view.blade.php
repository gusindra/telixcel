<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View Template') }}
        </h2>
    </x-slot>
    <style>
        #chart-container {
            font-family: Arial;
            border-radius: 5px;
            overflow: auto;
            text-align: center;
        }
        .l2r{
            position:initial !important;
        }

        .orgchart .welcome .title{background-color: #009933  !important;}
        .orgchart .question .title{background-color: #ffb02c !important;}
        .orgchart .api .title{background-color: #996633 !important;}
        .orgchart .incoming .title{background-color: blue !important;}
        .orgchart .text .title{background-color: gray !important;}
        .orgchart .helper .title{background-color: #66FF99 !important;}
    </style>
    <link rel="stylesheet" href="{{url('backend/css/jquery.orgchart.min.css')}}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="{{url('backend/js/jquery.orgchart.min.js')}}"></script>
    <script>
        (function($) {
            $(function() {
                var sample = [
                            { 'name': 'Welcome', 'title': 'department manager' },
                            { 'name': 'Bo Miao', 'title': 'department manager' },
                            { 'name': 'Su Miao', 'title': 'department manager',
                                'children': [
                                    { 'name': 'Tie Hua', 'title': 'senior engineer' },
                                    { 'name': 'Hei Hei', 'title': 'senior engineer',
                                        'children': [
                                            { 'name': 'Pang Pang', 'title': 'engineer' },
                                            { 'name': 'Xiang Xiang', 'title': 'UE engineer' }
                                        ]
                                    }
                                ]
                            },
                            { 'name': 'Hong Miao', 'title': 'department manager' },
                            { 'name': 'Chun Miao', 'title': 'department manager' }
                        ];
                console.log("{{$data}}");
                var ts = "{{$data}}".replace(/&quot;/g,'"');
                console.log(ts);
                ts = JSON.parse(ts.replace(/child/g,'children'));
                console.log(ts);
                var ds1 =
                    {
                        'name': 'Chat',
                        'title': 'Message from User',
                        'className': 'incoming',
                        'children': sample
                    };
                var ds =
                    {
                        'name': 'Chat',
                        'title': 'Message from User',
                        'className': 'incoming',
                        'children': ts
                    };
                console.log(ds1);
                console.log(ds);
                var oc = $('#chart-container').orgchart({
                    'data' : ds,
                    'nodeContent': 'title',
                    'direction': 'l2r',
                    'pan':true
                });

                oc.$chart.find('.node').on('click', function() {
                    if($(this).children('.title').text()!=='Chat'){
                        $('#selected-node').val($(this).children('.title').text());
                        $('#edit-panel').show();
                    }else{
                        $('#edit-panel').hide();

                    }
                });

                $('#btn-report-path').on('click', function() {
                    var $selected = $('#chart-container').find('.node.focused');
                    if ($selected.length) {
                        $selected.parents('.nodes').children(':has(.focused)').find('.node:first').each(function(index, superior) {
                        if (!$(superior).find('.horizontalEdge:first').closest('.node').parent().siblings().is('.hidden')) {
                            $(superior).find('.horizontalEdge:first').trigger('click');
                        }
                        });
                        $(this).prop('disabled', true);
                    } else {
                        alert('please select the node firstly');
                    }
                });

                $('#btn-reset').on('click', function() {
                    $('#edit-panel').hide();
                    $('#chart-container')
                        .find('.hidden').removeClass('hidden')
                        .end().find('.slide-up, .slide-right, .slide-left, .focused')
                        .removeClass('slide-up slide-right slide-left focused');
                    $('#chart-container')
                        .find('.isCollapsedSibling, .isChildrenCollapsed, .isSiblingsCollapsed')
                        .removeClass('isCollapsedSibling isChildrenCollapsed isSiblingsCollapsed');

                    $('#btn-report-path').prop('disabled', false);
                    $('#selected-node').val('');
                });

                $('#btn-edit').on('click', function() {
                    var $selected = $('#chart-container').find('.node.focused');
                    if ($selected.length) {
                        console.log($selected.attr('id'));
                        window.location = "/template/"+$selected.attr('id')+'/edit';
                    } else {
                        alert('please select the node firstly');
                    }
                });

                $('#edit-panel').hide();
            });
        })(jQuery);
    </script>
    <div>
        <div class="w-full h-screen mx-auto py-2 sm:px-6 lg:px-8">
            <div id="edit-panel" class="view-state">
                <button type="button" class="py-2 px-4 dark:bg-slate-600 hover:bg-gray-500 border border-gray-300" id="btn-report-path">Draw path</button>
                <button type="button" class="py-2 px-4 dark:bg-slate-600 hover:bg-gray-500  border border-gray-300" id="btn-reset">Reset</button>
                <a href="#" type="button" class="py-2 px-4 bg- dark:bg-slate-600 hover:bg-gray-500  border border-gray-300" id="btn-edit">Edit</a>
                <input type="text" id="selected-node" class="border p-2 mb-2 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:bg-slate-800 dark:text-slate-300 mt-1 block w-full dark:text-slate-500 w-full" placeholder="please select template" readonly="true">
            </div>
            <div id="chart-container" style="text-align: left;zoom: 140%;"></div>
        </div>
    </div>
</x-app-layout>
