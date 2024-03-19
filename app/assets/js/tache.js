$(document).ready(function() {
   $(document).on('click', '.update_button', function(event) {
        event.preventDefault();
        var form = $(this).closest('form');
        var id = form.find('input[name="tache[id]"]').val();
        var titre = form.find('input[name="tache[titre]"]').val();

        var confirmation = confirm("Êtes-vous sûr de vouloir modifier cette tâche ?");
        if (confirmation) {
            updateTask(id, titre);
        }
   });

   $(document).on('click', '.delete_button', function(event) {
        event.preventDefault();
        var form = $(this).closest('form');
        var id = form.find('input[name="tache[id]"]').val();

        var confirmation = confirm("Êtes-vous sûr de vouloir supprimer cette tâche ?");
        if (confirmation) {
            deleteTask(id, form);
        }
   });

   $(document).on('click', '#tache_save', function(event) {
        event.preventDefault();
        var form = $(this).closest('form');
        var titre = form.find('input[name="tache[titre]"]').val();

        var confirmation = confirm("Êtes-vous sûr de vouloir ajouter cette tâche ?");
        if (confirmation) {
            addTask(form, titre);
        }		
   });

   function updateTask(id, titre) {
        $.ajax({
            url: '/api/taches/' + id,
            method: 'PATCH',
            contentType: 'application/merge-patch+json',
            data: JSON.stringify({
                titre: titre
            }),
            dataType: 'json',
        }).done(function(response) {
            alert("Tâche modifiée");
        }).fail(function(xhr, status, error) {
            console.error(error);
            alert("Erreur lors de la modification de la tâche : " + error);
        });
   }

   function deleteTask(id, form) {
        $.ajax({
            url: '/api/taches/' + id,
            method: 'DELETE'
        }).done(function(response) {
            alert("Tâche supprimée");
            form.remove();
        }).fail(function(xhr, status, error) {
            console.error(error);
            alert("Erreur lors de la suppression de la tâche : " + error);
        });
   }

   function addTask(form, titre) {
        $.ajax({
            url: '/ajax/tache/add',
            method: 'POST',
            data: JSON.stringify({
                titre: titre
            }),
            dataType: 'html',
        }).done(function(response) {
            $('#task-list').append(response);
            form.find('input[name="tache[titre]"]').val('');
        }).fail(function(xhr, status, error) {
            console.error(error);
            alert("Erreur lors de l'ajout de la tâche : " + error);
        });
   }
});