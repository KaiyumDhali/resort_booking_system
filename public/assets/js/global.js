// Confarm message after form on click ========= 
var ConfarmMsg = function (FormID, msg, type, event) {
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: msg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: `Yes, ${type} it!`
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the form
            document.getElementById(FormID).submit();
        }
    })
};

// Show message after form submission ========= 
var AlertCall = function (message, alerttype) {
    if (message) {
        let title, icon;
        switch (alerttype) {
            case 'success':
                title = 'Saved!';
                icon = 'success';
                break;
            case 'info':
                title = 'Updated!';
                icon = 'info';
                break;
            case 'danger':
                title = 'Deleted!';
                icon = 'error';
                break;
            default:
                title = 'Notification';
                icon = 'info';
        }
        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    }

    // @if($errors - > any())
    // let errorHtml = '<ul>';
    // @foreach($errors - > all() as $error)
    // errorHtml += '<li>{{ $error }}</li>';
    // @endforeach
    // errorHtml += '</ul>';

    // Swal.fire({
    //     title: 'Error!',
    //     html: errorHtml,
    //     icon: 'error',
    //     confirmButtonColor: '#3085d6',
    //     confirmButtonText: 'OK'
    // });
    // @endif

}

// formatCurrency ========= 
function formatCurrency(number) {
    // Convert number to string and split into parts
    const parts = number.toString().split(".");
    // Extract integer and decimal parts
    const integerPart = parts[0];
    const decimalPart = parts.length > 1 ? "." + parts[1].padEnd(2, '0').substring(0, 2) : ".00";
    // Format integer part with commas
    const lastThree = integerPart.substring(integerPart.length - 3);
    const otherNumbers = integerPart.substring(0, integerPart.length - 3);
    const formattedOtherNumbers = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",");
    // Combine formatted parts
    return formattedOtherNumbers + (formattedOtherNumbers ? "," : "") + lastThree + decimalPart;
}

// formatDate ========= 
function formatDate(dateString) {
    let [year, month, day] = dateString.split('-');
    return `${day}-${month}-${year}`;
}

// formatDateAMPM ========= 
function formatDateAMPM(date) {
    let hours = date.getHours();
    let minutes = date.getMinutes();
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    let strTime = hours + ':' + minutes + ' ' + ampm;
    // return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + strTime;
    return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear() + ' ' + strTime;
}

