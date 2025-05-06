
/* fonction permet de crÃ©er un Nouveau  Produit et valider les inputs */

$cloudImages = [];
$cloudImagesCondition = [];

$tabImages = [];
$tabImagesCondition = [];
function testImages() {

    $('#myDrop').find('.dz-image').each(function () {
        console.log($(this).find('img').attr('data-url'));
        if ($(this).find('img').attr('data-url') != undefined) {
            $tabImages.push({ 'path': $(this).find('img').attr('data-url') });
        }
    })
    $i = 0;
    $('.photosConditonnement').each(function () {

        // $(this).find('img').attr('data-url');
        $idP = $(this).attr('id');
        $id = $idP.split('NewPhotos')[1];
        $(this).find('.dz-image-preview').each(function () {

            console.log($i)
            $i++;
            $(this).find('.dz-image-preview ').each(function () {
                console.log($i + " " + $(this).find('.dz-image').find('img').attr('data-url'));
            })

        })

    })


}

function CreationNewProduit() {
    var nomProduit = "";
    var caract = "";
    var tarifsFraisPort = "";
    var plusProduit = "";
    var test = false;
    $condition = [];
    var sousTitre = "";

    //Nombre des conditions ajoutÃ© dans le produit
    var nbCondition = $('#numberCondition').val();

    //le champs enregistrer dans type produit

    if ($('#InputNomProduit').val() == "") {
        $('#InputNomProduit').addClass('is-invalid');
        $('#InputNomProduit').removeClass('is-valid');
        $('#nomMessage').css('display', 'block');
        $('#InputNomProduit').css('border', '1px solid red');
        test = false;
    } else {
        $('#InputNomProduit').removeClass('is-invalid');
        $('#InputNomProduit').addClass('is-valid');
        $('#nomMessage').css('display', 'none');
        $('#InputNomProduit').css('border', '1px solid green');
        nomProduit = $('#InputNomProduit').val();
        test = true;
    }



    if ($('#caractProduit').val().trim().length < 1) {
        $('#caractProduit').addClass('is-invalid');
        $('#caractProduit').removeClass('is-valid');
        $('#caractMessage').css('display', 'block');
        $('#caractProduit').css('border', '1px solid red');
        test = false;

    } else {
        $('#caractProduit').removeClass('is-invalid');
        $('#caractProduit').addClass('is-valid');
        $('#caractMessage').css('display', 'none');
        $('#caractProduit').css('border', '1px solid green');
        caract = $('#caractProduit').val();
        test = true;
    }



    if ($('#tarifsFraisPort').val().trim().length < 1) {
        $('#tarifsFraisPort').addClass('is-invalid');
        $('#tarifsFraisPort').removeClass('is-valid');
        $('#tarifsFraisPortMessage').css('display', 'block');
        $('#tarifsFraisPort').css('border', '1px solid red');
        test = false;


    } else {
        $('#tarifsFraisPort').removeClass('is-invalid');
        $('#tarifsFraisPort').addClass('is-valid');
        $('#tarifsFraisPortMessage').css('display', 'none');
        $('#tarifsFraisPort').css('border', '1px solid green');

        tarifsFraisPort = $('#tarifsFraisPort').val();
        test = true;
    }


    if ($('#plusProduit').val().trim().length < 1) {
        $('#plusProduit').addClass('is-invalid');
        $('#plusProduit').removeClass('is-valid');
        $('#plusProduitMessage').css('display', 'block');
        $('#plusProduit').css('border', '1px solid red');
        test = false;
    } else {
        $('#plusProduit').removeClass('is-invalid');
        $('#plusProduit').addClass('is-valid');
        $('#plusProduitMessage').css('display', 'none');
        $('#plusProduit').css('border', '1px solid green');
        plusProduit = $('#plusProduit').val();
        test = true;
    }

    if ($('#reversement').val() == "") {
        $reversement = 0;
    } else {
        $reversement = $('#reversement').val();
    }

    //les champs de chaque condition

    for (var i = 1; nbCondition >= i; i++) {
        console.log(i);
        if ($('#descProduit' + i).val().trim().length < 1) {
            $('#descProduit' + i).addClass('is-invalid');
            $('#descProduit' + i).removeClass('is-valid');
            $('#descProduit' + i + 'Message').css('display', 'block');
            $('#descProduit' + i).css('border', '1px solid red');
            test = false;
        } else {
            $('#descProduit' + i).removeClass('is-invalid');
            $('#descProduit' + i).addClass('is-valid');
            $('#descProduit' + i + 'Message').css('display', 'none');
            $('#descProduit' + i).css('border', '1px solid green');
            $description = $('#descProduit' + i).val();
            test = true;
        }
        if ($('#soustitreProduit' + i).val() == "") {
            $sousTitre = "";
        }
        else {
            $sousTitre = $('#soustitreProduit' + i).val();
        }


        // console.log('sousTitre'+$sousTitre);

        if ($('#MontantHt' + i).val() == "") {
            $inputMontantHt = 0;
        } else {
            $inputMontantHt = $('#MontantHt' + i).val();
        }



        if ($('#tva' + i).val() == "") {
            $inputTVA = 0;
        } else {
            $inputTVA = $('#tva' + i).val();
        }






        if ($('#poidsContenant' + i).val() == "") {
            $inputpoidsContenant = 0;
        } else {
            $inputpoidsContenant = $('#poidsContenant' + i).val();
        }



        if ($('#poidsProduit' + i).val() == "") {
            $inputpoidsProduit = 0;
        } else {
            $inputpoidsProduit = $('#poidsProduit' + i).val();
        }



        if ($('#pochetteEnvoi' + i).val() == "") {
            $pochetteEnvoi = 0;
        } else {
            $pochetteEnvoi = $('#pochetteEnvoi' + i).val();
        }

        $condition.push({ 'idCondition': i, 'sousTitre': $sousTitre, 'description': $description, 'montantHT': $inputMontantHt, 'tva': $inputTVA, 'poidsProduit': $inputpoidsProduit, 'poidsContenant': $inputpoidsContenant, 'Pochette': $pochetteEnvoi });

    }



    $('#myDrop').find('.dz-image-preview').each(function () {
        if ($(this).attr('data-path') != undefined) {
            $cloudImages.push({ 'path': $(this).attr('data-path') });
        }
    })


    // $cloudImages.push({'path': result[i].secure_url});

    if ($cloudImages.length == 0) {

        Swal.fire({
            type: 'warning',
            title: 'Il faut ajouter une photo!',

        })
        test = false;
    }
    $('.photosConditonnement').each(function () {
        $idP = $(this).attr('id');
        $id = $idP.split('NewPhotos')[1];

        $(this).find('.dz-image-preview').each(function () {
            $cloudImagesCondition.push({ 'id': $id, 'path': $(this).attr('data-path') });
        })

    })

    if ($cloudImagesCondition.length == 0) {

        Swal.fire({
            type: 'warning',
            title: 'Il faut ajouter une photo!',

        })
        test = false;
    }

    $_data = {
        'labeletype': nomProduit,
        'caracteristiques': caract,
        'tarifsFraisPort': tarifsFraisPort,
        'plusproduit': plusProduit,
        'reversement': $reversement,
        'condition': $condition,
        'path': $cloudImages,
        'imagesCondition': $cloudImagesCondition
    }
    if (test) {
        $.ajax({
            type: "POST",
            url: pathCreationProduit,
            data: $_data,
            success: function () {
                Swal.fire({

                    type: 'success',
                    title: 'Produit  a Ã©tÃ© crÃ©e avec SuccÃ¨s ',

                })
                location.href = pathListeProduit;
            }

        });
    }

}


/* fin fonction creationNewProduit*/


function changerStatut($id) {
    $_data = { 'id': $id }

    $.ajax({
        type: "POST",
        url: pathPblier,
        data: $_data,
        success: function () {
            Swal.fire({

                type: 'success',
                title: 'Produit  publier ',

            })
        }

    });

}



