import { Component, OnInit, Input } from '@angular/core';
import { Subscription } from 'rxjs';
import { TemplateService } from '../services/template.service';

@Component({
  selector: 'app-footer',
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.css']
})
export class FooterComponent implements OnInit {
  @Input() page:string; //affiche un footer différent si on est sur le contenu ou la connexion
  footerColor:string;
  footerColorSubscription:Subscription;
  currentYear;
  constructor(private templateService:TemplateService) { }
  ngOnInit() {
    this.templateService.getFooterColor();
    this.footerColorSubscription=this.templateService.footerColorSubject.subscribe(
      (footerColor:string)=>{
        this.footerColor='#'+footerColor;
      }
    );
    this.templateService.emitFooterColor();
    this.currentYear = new Date().getFullYear();
  }

}
