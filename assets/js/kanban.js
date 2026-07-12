/**
 * AssetFlow - Kanban drag and drop
 */
(function () {
    'use strict';

    var board = document.getElementById('kanbanBoard');
    if (!board) return;

    var draggedCard = null;

    board.querySelectorAll('.kanban-card[draggable="true"]').forEach(function (card) {
        card.addEventListener('dragstart', function (e) {
            draggedCard = card;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });

        card.addEventListener('dragend', function () {
            card.classList.remove('dragging');
            board.querySelectorAll('.kanban-cards').forEach(function (col) {
                col.classList.remove('drag-over');
            });
        });
    });

    board.querySelectorAll('.kanban-cards').forEach(function (column) {
        column.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            column.classList.add('drag-over');
        });

        column.addEventListener('dragleave', function () {
            column.classList.remove('drag-over');
        });

        column.addEventListener('drop', function (e) {
            e.preventDefault();
            column.classList.remove('drag-over');

            if (!draggedCard) return;

            var newStage = column.dataset.stage;
            var cardId = draggedCard.dataset.id;

            if (!cardId || !newStage) {
                column.appendChild(draggedCard);
                return;
            }

            column.appendChild(draggedCard);

            document.getElementById('stageCardId').value = cardId;
            document.getElementById('stageValue').value = newStage;
            document.getElementById('stageForm').submit();
        });
    });
})();
