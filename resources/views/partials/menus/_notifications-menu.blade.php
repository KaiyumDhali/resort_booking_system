<!--begin::Messenger Icon (Direct link, no dropdown)-->
<a href="{{ url('/chat/users') }}"
   class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px position-relative me-2">

    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none">
        <path d="M12 2C6.48 2 2 6.03 2 11c0 2.61 1.28 4.94 3.28 6.6-.06.86-.36 2.24-1.19 3.62-.15.24.04.55.32.5 1.76-.33 3.13-1.06 3.9-1.56.02-.01.04-.01.06-.01A11.3 11.3 0 0 0 12 20c5.52 0 10-4.03 10-9s-4.48-9-10-9z"
              fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5"/>
        <circle cx="8" cy="11" r="1.3" fill="currentColor"/>
        <circle cx="12" cy="11" r="1.3" fill="currentColor"/>
        <circle cx="16" cy="11" r="1.3" fill="currentColor"/>
    </svg>

    <span id="messengerIcon"></span>

</a>
<!--end::Messenger Icon-->

<!--begin::Notification Icon (Dropdown - শুধু Requisitions)-->
<div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px position-relative"
    data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
    data-kt-menu-attach="parent"
    data-kt-menu-placement="bottom-end">

    {!! getIcon('notification-bing', 'fs-2') !!}

    <span id="notificationsIcon"></span>

</div>

<div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px"
     data-kt-menu="true">

    <div class="d-flex flex-column bgi-no-repeat rounded-top"
         style="background-image:url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}');">

        <h3 class="text-white fw-semibold px-9 mt-10 mb-6 py-4">
            Notifications
        </h3>

    </div>

    <div class="tab-content">

    <!-- TASK NOTIFICATIONS -->
    <div class="tab-pane fade show active" id="task_notifications_tab">
        <div class="scroll-y mh-325px my-5 px-8" id="task_notifications_list"></div>
        <div class="py-3 text-center border-top">
            <a href="{{ route('tasks.index') }}" class="btn btn-color-gray-600 btn-active-color-primary">
                View All
            </a>
        </div>
    </div>

    <!-- REQUISITION LIST -->
    <div class="tab-pane fade" id="requisition_notifications_tab">
        <div class="scroll-y mh-325px my-5 px-8" id="requisition_notifications_list"></div>
        <div class="py-3 text-center border-top">
            <a href="{{ route('production.requisition_list') }}" class="btn btn-color-gray-600 btn-active-color-primary">
                View All
            </a>
        </div>
    </div>

</div>

</div>
<!--end::Notification Icon-->

@push('scripts')

<script>
function updateNotificationBadge()
{
    let total = (window.taskNotificationCount || 0) + (window.requisitionNotificationCount || 0);

    if (total > 0) {
        $('#notificationsIcon').html(`
            <span class="badge badge-circle badge-danger position-absolute top-0 start-100 translate-middle">
                ${total}
            </span>
        `);
    } else {
        $('#notificationsIcon').html('');
    }
}
function chatNotifications()
{
    $.ajax({

        url: "{{ route('chat.notifications') }}",
        type: "GET",
        dataType: "json",

        success: function(data)
        {
            let totalUnread = 0;

            $.each(data, function(index, item){
                totalUnread += parseInt(item.unread);
            });

            // ✅ শুধু messenger icon-এর উপর badge বসছে
            if(totalUnread > 0){

                $('#messengerIcon').html(`

                    <span
                        class="badge badge-circle badge-danger position-absolute top-0 start-100 translate-middle">
                        ${totalUnread}
                    </span>

                `);

            }else{

                $('#messengerIcon').html('');
            }
        }
    });
}

function requisitionsNotifications()
{
    $.ajax({

        url: "{{ route('requisition_notifications') }}",
        type: "GET",
        dataType: "json",

        success: function(data)
        {
            console.log('REQUISITION DATA =>', data);

            let totalCount = 0;
            $('#requisition_notifications_list').empty();

            $.each(data, function(key, value){

                totalCount++;

                let requisitionDate = value[0].stock_date;
                let invoiceNo = value[0].invoice_no;
                let statusValue = value[0].status;

                let statusTextMap = {
                    0: 'Pending',
                    1: 'Approved by Production Manager',
                    2: 'Approved by CEO',
                    3: 'Approved by Store Incharge'
                };

                let statusText =
                    statusTextMap[statusValue] ?? 'Unknown';

                let showUrl =
                `{{ route('requisition_invoice_report_search',['invoiceNo'=>'_invoiceNo_']) }}`
                .replace('_invoiceNo_', invoiceNo);

                $('#requisition_notifications_list').append(`

                    <div class="d-flex flex-stack py-4">

                        <div class="d-flex align-items-center">

                            <div class="symbol symbol-35px me-4">

                                <span class="symbol-label bg-light-danger">

                                    {!! getIcon('information','fs-2 text-danger') !!}

                                </span>

                            </div>

                            <div class="mb-0 me-2">

                                <a href="${showUrl}"
                                   class="fs-6 text-gray-800 fw-bold">

                                   ${invoiceNo}

                                </a>

                                <div class="text-gray-400 fs-7">

                                   ${statusText}

                                </div>

                            </div>

                        </div>

                        <span class="badge badge-light">

                            ${requisitionDate}

                        </span>

                    </div>

                `);

            });

            if (totalCount === 0) {
                $('#requisition_notifications_list').html(`
                    <div class="text-center text-muted py-10">
                        No New Requisitions
                    </div>
                `);
            }

            // ✅ Notification icon এর উপরেও badge count বসানো হলো
            // if (totalCount > 0) {
            //     $('#notificationsIcon').html(`
            //         <span
            //             class="badge badge-circle badge-danger position-absolute top-0 start-100 translate-middle">
            //             ${totalCount}
            //         </span>
            //     `);
            // } else {
            //     $('#notificationsIcon').html('');
            // }

            window.requisitionNotificationCount = totalCount;
updateNotificationBadge();

        }

    });
}

chatNotifications();
requisitionsNotifications();

setInterval(function(){

    chatNotifications();
    requisitionsNotifications();

},3000);



function taskNotifications()
{
    $.ajax({
        url: "{{ route('task_notifications') }}",
        type: "GET",
        dataType: "json",

        success: function(data)
        {
            let totalCount = data.length;
            $('#task_notifications_list').empty();

            $.each(data, function(key, item){

                let d = item.data;

                let iconMap = {
                    created: 'plus-circle',
                    updated: 'notepad-edit',
                    completed: 'check-circle'
                };

                let colorMap = {
                    created: 'primary',
                    updated: 'warning',
                    completed: 'success'
                };

                let icon = iconMap[d.action] ?? 'information';
                let color = colorMap[d.action] ?? 'primary';

                $('#task_notifications_list').append(`
                    <div class="d-flex flex-stack py-4 task-notification-item" 
                         data-id="${item.id}" style="cursor:pointer;">

                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-35px me-4">
                                <span class="symbol-label bg-light-${color}">
                                    {!! getIcon('information','fs-2 text-') !!}
                                </span>
                            </div>

                            <div class="mb-0 me-2">
                                <div class="fs-6 text-gray-800 fw-bold">
                                    ${d.title}
                                </div>
                                <div class="text-gray-400 fs-7">
                                    ${d.message}
                                </div>
                            </div>
                        </div>

                    </div>
                `);
            });

            if (totalCount === 0) {
                $('#task_notifications_list').html(`
                    <div class="text-center text-muted py-10">
                        No New Task Notifications
                    </div>
                `);
            }

            // ✅ notificationsIcon badge count-এ requisition + task দুটোই যোগ হবে
            window.taskNotificationCount = totalCount;
            updateNotificationBadge();
        }
    });
}

// ✅ click করলে notification read মার্ক হবে
$(document).on('click', '.task-notification-item', function () {
    let id = $(this).data('id');
    $.post(`/task-notifications/${id}/read`, {
        _token: "{{ csrf_token() }}"
    }, function () {
        taskNotifications(); // refresh list
    });
});

taskNotifications();

setInterval(function(){
    taskNotifications();
}, 3000);
</script>

@endpush