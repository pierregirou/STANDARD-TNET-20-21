import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TelechargementAdministrationComponent } from './telechargement-administration.component';

describe('TelechargementAdministrationComponent', () => {
  let component: TelechargementAdministrationComponent;
  let fixture: ComponentFixture<TelechargementAdministrationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TelechargementAdministrationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TelechargementAdministrationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
