function AddValidate(obj,name)
{
    var objNext = obj.next();
    if(!obj.next().hasClass('validation-advice')){
        obj.after('<div class="validation-advice advice-validate-'+name+'">'+obj.data('validation-'+name)+'</div>');
        obj.next().fadeIn();
        obj.addClass('validation-failed');
    } else if(!objNext.hasClass('advice-validate-'+name) && objNext.hasClass('validation-advice')){
        obj.next().fadeOut(function(){
            $(this).remove();
            obj.after('<div class="validation-advice advice-validate-'+name+'">'+obj.data('validation-'+name)+'</div>');
            obj.next().fadeIn();
        });
    }
}
function RemoveValidate(obj)
{
    if(obj.next().hasClass('validation-advice')){
        obj.next().fadeOut(function(){
            $(this).remove();
        });
        obj.removeClass('validation-failed');
    }
}
$(document).ready(function(){
    $(document).on('click','.form-validate .form-validate-submit',function(){
        var ValidForm = false;
        var password = '';
        $('.form-list .required-entry').each(function(){
            var authValidate = $(this);
            if('' == authValidate.val().trim()){
                AddValidate(authValidate,'required');
                ValidForm = true;
            } else if(authValidate.hasClass('validate-login')){
                if(3 > authValidate.val().length){
                    AddValidate(authValidate,'login');
                    ValidForm = true;
                } else{
                    RemoveValidate(authValidate);
                }
            } else if(authValidate.hasClass('validate-password')){
                password = authValidate.val();
                if(6 > authValidate.val().length){
                    AddValidate(authValidate,'password');
                    ValidForm = true;
                } else{
                    RemoveValidate(authValidate);
                }
            } else if(authValidate.hasClass('validate-email')){
                var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
                if(!pattern.test(authValidate.val())){
                    AddValidate(authValidate,'email');
                    ValidForm = true;
                } else{
                    RemoveValidate(authValidate);
                }
            } else if(authValidate.hasClass('validate-cpassword')){
                
                if(password != authValidate.val()){
                    AddValidate(authValidate,'cpassword');
                    ValidForm = true;
                } else{
                    RemoveValidate(authValidate);
                }
            } else{
                RemoveValidate(authValidate);
                
            }
        });
        if(ValidForm){
            return false;
        }
    });
});