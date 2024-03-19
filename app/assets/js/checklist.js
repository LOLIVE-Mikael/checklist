// assets/js/checklist.js

$(document).ready(function() {
    // Écouteur d'événement pour le changement de sélection de la checklist
    function handleChecklistChange()  {
        var checklistId = $('#form_checklist').val();
        
			if(checklistId !== ''){
			// Requête AJAX pour récupérer les données de la checklist sélectionnée
			$.ajax({
				url: '/ajax/checklist',
				method: 'GET',
				data: { checklistId: checklistId },
				dataType: 'html',
				success: function(response) {
					// Mettre à jour les éléments HTML avec les données reçues
					$('#task-list').html(response);
				},
				error: function(xhr, status, error) {
					console.error(error);
				}
			});
		} else {
			$('#task-list').empty();
		}
    }
	
	$(document).on('click', '.dissociate-button', function(event) {
        // Empêcher le formulaire de se soumettre
        event.preventDefault();
	
		var taskId = $(this).data('task-id');
		var checklistId = $('#form_checklist').val();
		var confirmation = confirm("Êtes-vous sûr de vouloir dissocier cette tâche de la checklist ?");
		if (confirmation) {
			$.ajax({
				url: '/ajax/checklist/removetask',
				method: 'POST',
				data: {
					taskId: taskId,
					checklistId: checklistId
				},
				dataType: 'json',
				success: function(response) {
					// Mettre à jour l'interface utilisateur ou faire d'autres actions si nécessaire
					console.log('Task dissociated successfully');
					//on ajoute la tache retirée de la checklist dans la liste déroulante des taches (pour éventuellement pouvoir la remettre).
					// Récupérer le titre de la tâche

					var taskTitle = $('#task-' + taskId + ' > div').contents().filter(function() {
						return this.nodeType === 3; // Filtrer uniquement les nœuds de texte
					}).text().trim()

					$('#form_task').append($('<option>', {
						value: taskId,
						text: taskTitle
					}));
					//retirer la tache de la liste des taches associées à la checklist
					$('#task-' + taskId).remove();
				},
				error: function(xhr, status, error) {
					console.error(error);
					alert("Erreur Ajax: " + error);
				}
			});
		}
	});      

	$(document).on('change', '#form_task', function() {
	// Fonction pour mettre à jour l'état du champ form_newTask en fonction de la sélection dans form_task
		function updateNewTaskField() {
			// Récupérer la valeur sélectionnée dans form_task
			var selectedTaskValue = $('#form_task').val();
			// Désactiver le champ form_newTask si une tâche est sélectionnée dans form_task
			if (selectedTaskValue !== '') {
				$('#form_newTask').prop('disabled', true); 
			} else {
				$('#form_newTask').prop('disabled', false);
			}
		}
		updateNewTaskField()
	});

	$(document).on('change', '#form_newTask', function() {
		function updateFormNewTask(){
			var newTaskValue = $('#form_newTask').val();
			if (newTaskValue !== '') {
				$('#form_task').prop('disabled', true); 
			} else {
				$('#form_task').prop('disabled', false); 
			}
		}
		updateFormNewTask();
	});

	$(document).on('click', '.formajout_submit', function(event) {
        // Empêcher le formulaire de se soumettre
        event.preventDefault();
	
		var checklistId = $('#form_checklist').val();
		var taskId = $('#form_task').val();
		var newTask = $('#form_newTask').val();
	
		var confirmation = confirm("Êtes-vous sûr de vouloir associer cette tâche de la checklist ?");
		if (confirmation) {
			$.ajax({
				url: '/ajax/checklist/addtask',
				method: 'POST',
				data: {
					taskId: taskId,
					checklistId: checklistId,
					newTask: newTask
				},
				dataType: 'html',
				success: function(response) {
					// Mettre à jour l'interface utilisateur ou faire d'autres actions si nécessaire
					console.log('Task associated successfully');
					//on ajoute la tache ajoutée à la liste.
					// Récupérer le titre de la tâche
					$('#ul-task-list').append(response);
					//et on la retire de la liste déroulante (si c'est une tache déja existante).
					if(taskId){
						$('#form_task option[value="' + taskId + '"]').remove();
						$('#form_newTask').prop('disabled', false); 
					}
				},
				error: function(xhr, status, error) {
					console.error(error);
					alert("Erreur Ajax: " + error);
				}
			});
		}
	});



	
	//supprimer le bouton "voir", inutile avec le JS
    $('#form_submit').hide();
	
	// Appeler la fonction handleChecklistChange au chargement de la page
    handleChecklistChange();

    // Écouteur d'événement pour le changement de sélection de la checklist
    $('#form_checklist').change(handleChecklistChange);
	
	
	
	
});