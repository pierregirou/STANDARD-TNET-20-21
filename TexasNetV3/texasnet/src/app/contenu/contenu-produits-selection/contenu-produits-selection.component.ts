import { Component, OnInit } from '@angular/core';
import { ModuleService } from '../../services/modules.service';

@Component({
  selector: 'app-contenu-produits-selection',
  templateUrl: './contenu-produits-selection.component.html',
  styleUrls: ['./contenu-produits-selection.component.css']
})
export class ContenuProduitsSelectionComponent implements OnInit {

  afficherFiltres:boolean;
  constructor(private moduleService : ModuleService) { }

  ngOnInit() {      
    // determine si on affiche les filtres
    this.moduleService.getAfficherFiltres().then(
      (data:boolean)=>{
          this.afficherFiltres=data;
      }
    )
  }

}
