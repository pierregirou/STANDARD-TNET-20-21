import { Component, OnInit } from '@angular/core';
import { SelectionMenuService } from '../../services/selection-menu.service';
import { ActivatedRoute } from '@angular/router';
import { ModuleService } from '../../services/modules.service';

@Component({
  selector: 'app-contenu-promo-selection',
  templateUrl: './contenu-promo-selection.component.html',
  styleUrls: ['./contenu-promo-selection.component.css']
})
export class ContenuPromoSelectionComponent implements OnInit {
  afficherFiltres:boolean;
  constructor(private selectionMenuService:SelectionMenuService,private route:ActivatedRoute,private moduleService : ModuleService) { }
  ngOnInit() {
    this.route.params.subscribe();
          // determine si on affiche les filtres
          this.moduleService.getAfficherFiltres().then(
            (data:boolean)=>{
                this.afficherFiltres=data;
            }
          )
  }

}
