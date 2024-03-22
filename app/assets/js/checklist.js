// assets/js/checklist.js

$(document).ready(function() {
    
	//fonction de mise à jour de l'affitache des taches en fonction de la cheklist sélectionnée
    function handleChecklistChange()  {
        var checklistId = $('#form_checklist').val();
        
		if(checklistId !== ''){
			// Requête AJAX pour récupérer les données de la checklist sélectionnée
			$.ajax({
				url: '/ajax/checklist',
				method: 'GET',
				data: { checklistId: checklistId },
				dataType: 'html',
			}).done(function(response) {
					$('#task-list').html(response);
			}).fail(function(xhr, status, error) {
					console.error(error);
			});
		} else {
			$('#task-list').empty();
		}
    }
	
	//fonction pour retirer la tache.
	function removeTask() {
		var taskId = $(this).data('task-id');
		var checklistId = $('#form_checklist').val();
		var confirmation = confirm("Êtes-vous sûr de vouloir dissocier cette tâche de la checklist ?");
		if (confirmation) {
			$.ajax({
				url: '/api/checklists/' + taskId + '/tasks/' + checklistId,
				method: 'DELETE',
				dataType: 'json'
			}).done(function(response) {
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
			}).fail(function(xhr, status, error) {
				console.error(error);
				alert("Erreur Ajax: " + error);
			});
		}
	}
	
	// Fonction pour rendre la création d'une nouchelle tache possible si aucune tache n'est sélectionnée.
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

	// Fonction pour rendre la sélection d'un tache possible si le titre d'une nouvelle tache est vide.
	function updateFormNewTask(){
		var newTaskValue = $('#form_newTask').val();
		if (newTaskValue !== '') {
			$('#form_task').prop('disabled', true); 
		} else {
			$('#form_task').prop('disabled', false); 
		}
	}

	// Fonction pour associer une nouvelle tache à la checklist.
	function addTask(){
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
			}).done(function(response) {
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
			}).fail(function(xhr, status, error) {
				console.error(error);
				alert("Erreur Ajax: " + error);
			});
		}
	}

	//ecouteur d'évènement en cas de sélection d'une nouvelle tache
	$(document).on('change', '#form_task', function() {
		updateNewTaskField()
	});

	//ecouteur d'évènement en cas de saisis d'une nouvelle tâche
	$(document).on('change', '#form_newTask', function() {
		updateFormNewTask();
	});

	$(document).on('click', '.formajout_submit', function(event) {
        // Empêcher le formulaire de se soumettre
        event.preventDefault();
		addTask()
	});

	//supprimer le bouton "voir", inutile avec le JS
    $('#form_submit').hide();
	
	// au démarrage, mise à jour de la page en fonction de la checklist sélectionnée.
    handleChecklistChange();

    // Écouteur d'événement pour le changement de sélection de la checklist
    $('#form_checklist').change(handleChecklistChange);
	
	// Écouteur d'événement en cas de clic sur un bouton pour retirer une tache	
	$(document).on('click', '.dissociate-button', function(event) {
        // Empêcher le formulaire de se soumettre
        event.preventDefault();
		removeTask();
	});  
	
});