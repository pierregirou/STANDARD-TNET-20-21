import { Component, OnInit } from '@angular/core';
import { TemplateService } from '../../services/template.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-contenu-points',
  templateUrl: './contenu-points.component.html',
  styleUrls: ['./contenu-points.component.css']
})
export class ContenuPointsComponent implements OnInit {
  contenuColorSubscription:Subscription;
  contenuColor:String;
  constructor(private templateService:TemplateService) { }

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
