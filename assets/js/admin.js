function cf7getepay_getPaymentMoreInfo(e, post_id)
{
    e.preventDefault();
    
    jQuery.ajax({
        url : ajax_object_cf7rzp.ajax_url,
        type : "get",
        dataType : "json",
        data : {
            action: "cf7getepay_get_payment_more_info",
            post_id: post_id
        },
        success : function(res){

            var tbl_content = '<tr><th>Payment Id:</th><td>'+res.getepay_payment_id+'</td></tr>'+
                    '<tr><th>Name:</th><td>'+res.getepay_cf7_name+'</td></tr>'+
                    '<tr><th>Mobile No.:</th><td>'+res.getepay_cf7_phone+'</td></tr>'+
                    '<tr><th>Email:</th><td>'+res.getepay_cf7_email+'</td></tr>'+
                    '<tr><th>Form Name:<td>'+res.getepay_cf7_form_title+'</td></tr>'+
                    '<tr><th>Amount:</th><td>'+res.getepay_cf7_amount+'</td></tr>'+
                    '<tr><th>Payment Mode:</th><td>'+res.getepay_cf7_payment_mode+'</td></tr>'+
                    '<tr><th>Payment Status:</th><td>'+res.getepay_cf7_payment_status+'</td></tr>';       

            Swal.fire({
                title: 'Payment Details',
                customClass: {
                    container: 'cf7rzp-payment-more-info'
                },    
                html: '<table>'+tbl_content+'</table>'
            })                
        }
    });    
}