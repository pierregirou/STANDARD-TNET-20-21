import { Component, OnInit } from '@angular/core';
import { ModuleService } from '../../services/modules.service';
import { ImageService } from '../../services/images.service';
import { TemplateService } from '../../services/template.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-contenu-maintenance',
  templateUrl: './contenu-maintenance.component.html',
  styleUrls: ['./contenu-maintenance.component.css']
})
export class ContenuMaintenanceComponent implements OnInit {
  contenuColorSubscription:Subscription;
  contenuColor:string;
  constructor(private templateService:TemplateService,private moduleService:ModuleService, public imageService:ImageService) { }

  ngOnInit() {
    this.templateService.getContenuColor();
    this.contenuColorSubscription=this.templateService.contenuColorSubject.subscribe(
      (contenuColor:string)=>{
        this.contenuColor='#'+contenuColor;
      }
    );
    this.templateService.emitContenuColor();
   }

}
