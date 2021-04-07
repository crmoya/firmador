/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).on('keyup', 'input.rut', function () {
    var rut = $(this).val();
    rut = $.formatRut(rut);
    $(this).val(rut);
});