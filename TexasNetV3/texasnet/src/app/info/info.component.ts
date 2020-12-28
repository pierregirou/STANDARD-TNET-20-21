import { Component, OnInit } from '@angular/core';
import { Subscription } from 'rxjs';
import { TemplateService } from '../services/template.service';
import { HttpRequest } from '../services/http-request.service';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-info',
  templateUrl: './info.component.html',
  styleUrls: ['./info.component.css']
})
export class InfoComponent implements OnInit {
  infoColor: string;
  infoColorSubscription: Subscription;
  nomsociete: string;
  adresse1: string;
  adresse2: string;
  email: string;
  fax: string;
  telephone: string;
  siteweb: string;
  messageSoc: string = '';

  constructor(private httpRequest: HttpRequest, private templateService: TemplateService, private httpClient: HttpClient) { }

  ngOnInit() {

    this.httpClient.post(this.httpRequest.InfoParametrages, {
      "parametrages": "getParam"
    }).subscribe(data => {
      this.nomsociete = data[1].nomSociete;
      this.adresse1 = data[1].adresse1;
      this.adresse2 = data[1].adresse2;
      this.siteweb = data[1].siteweb;
      this.telephone = data[1].telephone;
      this.email = data[1].email;
      this.messageSoc = data[1].messageSoc;
    })

    this.templateService.getInfoColor();
    this.infoColorSubscription = this.templateService.infoColorSubject.subscribe(
      (infoColor: string) => {
        this.infoColor = '#' + infoColor;
      }
    );
    this.templateService.emitInfoColor();
  }

}
