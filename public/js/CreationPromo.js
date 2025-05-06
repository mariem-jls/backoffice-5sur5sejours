
function changerEtatPromo($id, $etat) {

    $_data = {
        'idPromo': $id,
        'etat': $etat
    };
    $.ajax({
        type: "POST",
        url: changeEtatPromotion,
        data: $_data,
        success: function () {

            Swal.fire({
                type: 'success',
                title: 'Opération réalisée avec succès',
            })

            location.reload();


        }
    });

}
function AjouterPromo() {
    $('#loaderAcc').css('display', 'block');
    var $_data = {};
    var $testDeCreation = "false";

    /* les variables qui contient valeurs des champs*/
    var code = "";
    var datedebut = "";
    var datefin = "";
    var pourcentage = "";

    /* les variables de test du contenu des champs */
    var testcode = false;
    var testdatedebut = false;
    var testdatefin = false;
    var testpourcentage = false;
    /*récupérer et valider les valeurs des champs */

    if ($('#code').val() == "") {
        $('#code').addClass('is-invalid');
        $('#code').removeClass('is-valid');
        $('#codeMessage').css('display', 'block');
        $('#code').css('border', '1px solid red');
        testcode = false;
    } else {
        $('#code').removeClass('is-invalid');
        $('#code').addClass('is-valid');
        $('#codeMessage').css('display', 'none');
        $('#code').css('border', '1px solid green');
        code = $('#code').val();
        testcode = true;
    }

    if ($('#datedebut').val() == "") {
        $('#datedebut').addClass('is-invalid');
        $('#datedebut').removeClass('is-valid');
        $('#datedebutMessage').css('display', 'block');
        $('#datedebut').css('border', '1px solid red');
        testdatedebut = false;
    } else {
        $('#datedebut').removeClass('is-invalid');
        $('#datedebut').addClass('is-valid');
        $('#datedebutMessage').css('display', 'none');
        $('#datedebut').css('border', '1px solid green');
        datedebut = $('#datedebut').val();
        testdatedebut = true;
    }

    if ($('#datefin').val() == "") {
        $('#datefin').addClass('is-invalid');
        $('#datefin').removeClass('is-valid');
        $('#datefinMessage').css('display', 'block');
        $('#datefin').css('border', '1px solid red');
        testdatefin = false;
    } else {
        $('#datefin').removeClass('is-invalid');
        $('#datefin').addClass('is-valid');
        $('#datefinMessage').css('display', 'none');
        $('#datefin').css('border', '1px solid green');
        datefin = $('#datefin').val();
        testdatefin = true;
    }

    if ($('#pourcentage').val() == "") {
        $('#pourcentage').addClass('is-invalid');
        $('#pourcentage').removeClass('is-valid');
        $('#pourcentageMessage').css('display', 'block');
        $('#pourcentage').css('border', '1px solid red');
        testpourcentage = false;
    } else {
        $('#pourcentage').removeClass('is-invalid');
        $('#pourcentage').addClass('is-valid');
        $('#pourcentageMessage').css('display', 'none');
        $('#pourcentage').css('border', '1px solid green');
        pourcentage = parseInt($('#pourcentage').val());
        testpourcentage = true;
    }

    var type = $('#type').val();
    var nbreApplicable = $('#nbreApplicable').val();
    var strategie = "";

    var $tabCodeSejour = [];
    var $tabParents = [];

    if (type == "codeSejour") {
        $('#sejours').find('option').each(function () {
            if ($(this).prop('selected') == true) {
                $tabCodeSejour.push($(this).val());
            }
        });
    } else if (type == "parents") {
        $('#parents').find('option').each(function () {
            if ($(this).prop('selected') == true) {
                $tabParents.push($(this).val());
            }
        });
    }

    if (type == "parents" && $tabParents.length == 0) {
        $('#loaderAcc').css('display', 'none');
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Merci de selectionner au moins un parent.',
        });
    } else if (type == "codeSejour" && $tabCodeSejour.length == 0) {
        $('#loaderAcc').css('display', 'none');
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Merci de selectionner au moins un séjour.',
        });
    } else if (testdatefin && testdatedebut && testcode) {
        $_data = {
            'code': code,
            'dateDebut': datedebut,
            'dateFin': datefin,
            'nbreMaxParUser': parseInt($('#nbrutilisation').val()),
            'type': type,
            'pourcentage': pourcentage,
            'etat': 1,
            'tabParents': $tabParents,
            'tabCodeSejour': $tabCodeSejour,
            'nbreApplicable': nbreApplicable,
            'strategie': "",
        };
        console.log(JSON.stringify($_data));

        $.ajax({
            type: "POST",
            url: "/promotions/new",
            data: JSON.stringify($_data),
            contentType: "application/json",
            success: function (response) {
                if (response.message == 'create') {
                    $('#loaderAcc').css('display', 'none');
                    Swal.fire({
                        icon: 'success',
                        title: 'Votre promotion a été créée avec succès !',
                        showClass: {
                            popup: 'animated fadeInDown faster'
                        },
                        hideClass: {
                            popup: 'animated fadeOutUp faster'
                        }
                    });
                    location.href = "/promotions";
                } else if (response.message == 'code promo deja utilise') {
                    alert('code promo deja utilise');
                    $('#loaderAcc').css('display', 'none');
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Le code promo ' + code + ' a déjà été utilisé ',
                    });
                } else {
                    $('#loaderAcc').css('display', 'none');
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Il semble qu\'il y eu un problème!',
                    });
                }
            }
        });
    } else {
        $('#loaderAcc').css('display', 'none');
    }
}




function modifierPromo($id) {
    $('#loaderAcc').css('display', 'block');
    var $_data = {};
    var $testDeCreation = "false";

    /* les variables qui contient valeurs des champs */
    var code = "";
    var datedebut = "";
    var datefin = "";
    var pourcentage = "";

    /* les variables de test du contenu des champs */
    var testcode = false;
    var testdatedebut = false;
    var testdatefin = false;
    var testpourcentage = false;

    /* récupérer et valider les valeurs des champs */
    if ($('#code').val() == "") {
        $('#code').addClass('is-invalid');
        $('#code').removeClass('is-valid');
        $('#codeMessage').css('display', 'block');
        $('#code').css('border', '1px solid red');
        testcode = false;
    } else {
        $('#code').removeClass('is-invalid');
        $('#code').addClass('is-valid');
        $('#codeMessage').css('display', 'none');
        $('#code').css('border', '1px solid green');
        code = $('#code').val();
        testcode = true;
    }

    if ($('#datedebut').val() == "") {
        $('#datedebut').addClass('is-invalid');
        $('#datedebut').removeClass('is-valid');
        $('#datedebutMessage').css('display', 'block');
        $('#datedebut').css('border', '1px solid red');
        testdatedebut = false;
    } else {
        $('#datedebut').removeClass('is-invalid');
        $('#datedebut').addClass('is-valid');
        $('#datedebutMessage').css('display', 'none');
        $('#datedebut').css('border', '1px solid green');
        datedebut = $('#datedebut').val();
        testdatedebut = true;
    }

    if ($('#datefin').val() == "") {
        $('#datefin').addClass('is-invalid');
        $('#datefin').removeClass('is-valid');
        $('#datefinMessage').css('display', 'block');
        $('#datefin').css('border', '1px solid red');
        testdatefin = false;
    } else {
        $('#datefin').removeClass('is-invalid');
        $('#datefin').addClass('is-valid');
        $('#datefinMessage').css('display', 'none');
        $('#datefin').css('border', '1px solid green');
        datefin = $('#datefin').val();
        testdatefin = true;
    }

    if ($('#pourcentage').val() == "") {
        $('#pourcentage').addClass('is-invalid');
        $('#pourcentage').removeClass('is-valid');
        $('#pourcentageMessage').css('display', 'block');
        $('#pourcentage').css('border', '1px solid red');
        testpourcentage = false;
    } else {
        $('#pourcentage').removeClass('is-invalid');
        $('#pourcentage').addClass('is-valid');
        $('#pourcentageMessage').css('display', 'none');
        $('#pourcentage').css('border', '1px solid green');
        pourcentage = parseInt($('#pourcentage').val());
        testpourcentage = true;
    }

    var type = $('#type').val();
    var nbreApplicable = $('#nbreApplicable').val();
    var strategie = $('#strategie').val();

    var $tabCodeSejour = [];
    var $tabParents = [];

    if (type == "codeSejour") {
        $('#sejours').find('option').each(function () {
            if ($(this).prop('selected') == true) {
                $tabCodeSejour.push($(this).val());
            }
        });
    } else if (type == "parents") {
        $('#parents').find('option').each(function () {
            if ($(this).prop('selected') == true) {
                $tabParents.push($(this).val());
            }
        });
    }

    if (type == "parents" && $tabParents.length == 0) {
        $('#loaderAcc').css('display', 'none');
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Merci de selectionner au moins un parent.',
        });
    } else if (type == "codeSejour" && $tabCodeSejour.length == 0) {
        $('#loaderAcc').css('display', 'none');
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Merci de selectionner au moins un séjour.',
        });
    } else if (testdatefin && testdatedebut && testcode) {
        $_data = {
            'idPromo': $id,
            'code': code,
            'dateDebut': datedebut,
            'dateFin': datefin,
            'nbreMaxParUser': parseInt($('#nbrutilisation').val()),
            'type': type,
            'pourcentage': pourcentage,
            'etat': 1,
            'tabParents': $tabParents,
            'tabCodeSejour': $tabCodeSejour,
            'nbreApplicable': nbreApplicable,
            'strategie': ""
        };
        console.log(JSON.stringify($_data));

        $.ajax({
            type: "POST",
            url: "/promotions/" + $id + "/edit",
            data: JSON.stringify($_data),
            contentType: "application/json",
            success: function (response) {
                console.log(response);
                $('#loaderAcc').css('display', 'none');
                if (response.message === 'Update successful') {
                    console.log('Votre promotion a été modifiée avec succès !');
                    location.href = "/promotions";
                } else {
                    console.log(response);
                    $('#loaderAcc').css('display', 'none');
                    console.log('Il semble qu\'il y eu un problème!');
                }
            }
            // ,
            // error: function (xhr, status, error) {
            //     $('#loaderAcc').css('display', 'none');
            //     let errorMessage = 'Il semble qu\'il y eu un problème!';
            //     if (xhr.responseJSON && xhr.responseJSON.error) {
            //         errorMessage = xhr.responseJSON.error;
            //     }
            //     console.log(errorMessage);
            // }
        });
        
        
        
    } else {
        $('#loaderAcc').css('display', 'none');
    }
}




function getParents() {
    console.log("get parents");
    $.ajax({
        type: "GET",
        url: getListeParents,

        success: function (response) {
            // console.log("Response received: " + response['parents']);
            var tab = response['parents'];
            // console.log(tab);

            if (tab) {
                $options = "";
                for ($i = 0; $i < tab.length; $i++) {

                    $options += "<option value=" + tab[$i]['id'] + ">" + tab[$i]['nom'] + " " + tab[$i]['prenom'] + "-" + tab[$i]['email'] + "</option>"


                }

                $('#parents').html($options);
            }
            $('#loaderAcc').css('display', 'none');

        },

        error: function (xhr, status, error) {
            console.error("Error occurred: " + status + " " + error);
            console.error(xhr.responseText);
            alert("An error occurred while fetching the sejours. Please try again.");
            $('#loaderAcc').css('display', 'none');
        }
    });
}


function getCodeSejour() {
    console.log("get code sejour");
    $.ajax({
        type: "GET",
        url: getListeCodeSejours, // Ensure this variable is defined and has the correct URL

        success: function (response) {
            console.log("Response received: " + response['sejours']);
            var tab = response['sejours'];
            console.log(tab);

            if (tab) {
                var options = "";
                for (var i = 0; i < tab.length; i++) {
                    options += "<option value=" + tab[i]['id'] + ">" + tab[i]['codeSejour'] + "</option>";
                }

                $('#sejours').html(options);
            } else {
                $('#sejours').html('<option>No sejours found</option>');
            }
            $('#loaderAcc').css('display', 'none');
        },

        error: function (xhr, status, error) {
            console.error("Error occurred: " + status + " " + error);
            console.error(xhr.responseText);
            alert("An error occurred while fetching the sejours. Please try again.");
            $('#loaderAcc').css('display', 'none');
        }
    });
}




function getParentsUpdate(parents) {

    $.ajax({
        type: "GET",
        url: "/promotions/getListeParents",

        success: function (response) {
            $tab = response['parents'];
            // console.log("parents: "+parents);
            if ($tab) {
                $options = "";
                for ($i = 0; $i < $tab.length; $i++) {

                    if (parents.includes($tab[$i]['id'])) {
                        $options += "<option selected value=" + $tab[$i]['id'] + ">" + $tab[$i]['nom'] + " " + $tab[$i]['prenom'] + "-" + $tab[$i]['email'] + "</option>";

                    }
                    else {

                        $options += "<option  value=" + $tab[$i]['id'] + ">" + $tab[$i]['nom'] + " " + $tab[$i]['prenom'] + "-" + $tab[$i]['email'] + "</option>";
                    }



                }

                $('#parents').html($options);
            }
            $('#loaderAcc').css('display', 'none');

        }
    });
}


function getCodeSejourUpdate(sejour) {

    $.ajax({
        type: "GET",
        url: getListeCodeSejours,

        success: function (response) {
            $tab = response['sejours'];
            // console.log("sejour: "+sejour);

            if ($tab) {
                $options = "";
                for ($i = 0; $i < $tab.length; $i++) {

                    if (sejour.includes($tab[$i]['id'])) {

                        $options += "<option selected value=" + $tab[$i]['id'] + ">" + $tab[$i]['codeSejour'] + "</option>"
                    }

                    else {
                        $options += "<option value=" + $tab[$i]['id'] + ">" + $tab[$i]['codeSejour'] + "</option>"
                    }



                }

                $('#sejours').html($options);
            }
            $('#loaderAcc').css('display', 'none');

        }
    });
}