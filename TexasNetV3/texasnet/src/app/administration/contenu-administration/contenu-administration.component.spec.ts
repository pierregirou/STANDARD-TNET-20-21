import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuAdministrationComponent } from './contenu-administration.component';

describe('ContenuAdministrationComponent', () => {
  let component: ContenuAdministrationComponent;
  let fixture: ComponentFixture<ContenuAdministrationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuAdministrationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuAdministrationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
